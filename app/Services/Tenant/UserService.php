<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\Mail\UserDetails;
use App\Models\Biller;
use App\Models\CustomerGroup;
use App\Models\User;
use App\Models\Customer;
use App\Models\Warehouse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;

class UserService
{
    protected SendMailAction $sendMailAction;

    public function __construct(SendMailAction $sendMailAction)
    {
        $this->sendMailAction = $sendMailAction;
    }

    public function authorize($ability)
    {
        if (!Auth::guard('web')->user()->can($ability)) {
            throw new AuthorizationException(__('Sorry! You are not allowed to access this module.'));
        }
    }

    public function getAllUsers()
    {
        $this->authorize('users-index');
        return User::with('roles')->get();
    }

    public function getUserFormData()
    {
        $this->authorize('users-add');

        return [
            'roles' => Role::active()->get(['id', 'name']),
            'billers' => Biller::get(['id', 'name', 'phone_number']),
            'warehouses' => Warehouse::get(['id', 'name']),
            'customerGroups' => CustomerGroup::get(['id', 'name']),
        ];
    }

    public function createUser(array $data)
    {
        $this->authorize('users-add');

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone_number'],
                'company_name' => $data['company_name'] ?? null,
                'biller_id' => $data['biller_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'is_active' => $data['is_active'] ?? false,
                'password' => bcrypt($data['password']),
            ]);

            // تعيين الصلاحية للمستخدم
            $user->assignRole($data['role']);

            // إذا كان المستخدم عميلًا (Customer)، يتم إنشاء سجل في جدول العملاء
            if ($data['role'] === 'Customer') {
                Customer::create([
                    'name' => $data['customer_name'],
                    'user_id' => $user->id,
                    'customer_group_id' => $data['customer_group_id'],
                    'tax_no' => $data['tax_no'] ?? null,
                    'company_name' => $data['company_name'] ?? null,
                    'phone_number' => $data['phone_number'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'state' => $data['state'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'country' => $data['country'] ?? null,
                ]);
            }

            // إرسال الإيميل
            if (!$this->sendMailAction->execute($data, UserDetails::class)) {
                $message = __('User created successfully. Please setup your mail settings to send mail.');
            } else {
                $message = __('User created successfully.');
            }

            DB::commit();
            return $message;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error while creating the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function getUserEditData($id)
    {
        $this->authorize('users-edit');

        return [
            'user' => User::with('roles')->findOrFail($id),
            'roles' => Role::active()->get(['id', 'name']),
            'billers' => Biller::get(['id', 'name', 'phone_number']),
            'warehouses' => Warehouse::get(['id', 'name']),
        ];
    }

    public function updateUser($id, array $data)
    {
        $this->authorize('users-edit');
        try {
            $input = collect($data)->except('password')->toArray();
            if (!empty($request['password']))
                $input['password'] = bcrypt($request['password']);

            $user = User::findOrFail($id);
            $user->update($input);

            Cache::forget('user_role');
        } catch (Exception $e) {
            Log::error('Error while updating the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function updateProfile($id, array $data)
    {
        try {
            $user = User::findOrFail($id);
            $input = collect($data)->except('role')->toArray();
            $user->update($input);
        } catch (Exception $e) {
            Log::error('Error while updating the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function changeUserPassword($id, array $input)
    {
        $user = User::findOrFail($id);

        if ($input['new_pass'] !== $input['confirm_pass']) {
            return Redirect::back()->with('message2', __('Please confirm your new password.'));
        }

        if (Hash::check($input['current_pass'], $user->password)) {
            $user->password = Hash::make($input['new_pass']);
            $user->save();
        } else {
            return Redirect::back()->with('message1', __('Current Password doesn\'t match.'));
        }

        auth()->logout();
        return Redirect::to('/Tenant_login');
    }

    public function deleteUsers(array $userIds)
    {
        DB::beginTransaction();

        try {
            // تحديث المستخدمين دفعة واحدة
            User::whereIn('id', $userIds)
                ->update(['is_active' => false]);

            // حذف المستخدمين دفعة واحدة
            User::whereIn('id', $userIds)
                ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => false]);
            $user->delete();

            DB::commit();

            if (Auth::id() === $id) {
                auth()->logout();
                return Redirect::to('/Tenant_login');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function getTrashedUsers()
    {
        $this->authorize('users-index');
        return User::onlyTrashed()->get();
    }

    public function restoreUser($id)
    {
        $this->authorize('users-index');

        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }

    }


}

