<?php

namespace App\Http\Controllers\AuthTenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RegisterTenantRequest;
use App\Models\Biller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\SuperUser;
use App\Models\User;
use App\Models\Warehouse;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class TenantRegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::active()->get(['id', 'name']);
        $customerGroups = CustomerGroup::get(['id', 'name']);
        $billers = Biller::get(['id', 'name', 'phone_number']);
        $warehouses = Warehouse::get(['id', 'name']);
        return view('tenant.auth.register', compact(
            'roles', 'customerGroups', 'billers', 'warehouses'
        ));
    }


    public function store(RegisterTenantRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // إنشاء المستخدم
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone_number'],
                'company_name' => $data['company_name'] ?? null,
                'biller_id' => $data['biller_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'is_active' => false, // الحساب غير مفعل
                'password' => bcrypt($data['password']),
            ]);

            // تعيين الصلاحية للمستخدم
            $user->assignRole($data['role']);

            // إذا كان المستخدم عميلًا (Customer)، يتم إضافة بيانات العميل
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

            DB::commit();

            Auth::guard('web')->login($user);

            return redirect()->route('tenant.dashboard')
                ->with('success', 'The account has been created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error while creating the account: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Error while creating the account,try again.'])
                ->withInput();
        }
    }



}
