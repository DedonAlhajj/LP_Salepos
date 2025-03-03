<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\Mail\BillerCreate;
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

class BillerService
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

    public function getBillers()
    {
        return Biller::all();
    }
    public function getAllBillerss()
    {
        $this->authorize('billers-index');
        return Biller::with('media')->get();
    }

    public function create()
    {
        $this->authorize('billers-add');
    }

    public function createBiller(array $data)
    {
        $this->authorize('billers-add');
        try {
            $image = $data['image'] ?? null;

            // إزالة حقل 'image' فقط إذا كان موجودًا في المصفوفة
            if (isset($data['image'])) {
                unset($data['image']);
            }

            // إنشاء الكيان بدون الصورة
            $biller = Biller::create($data);

            // إذا كانت الصورة موجودة، يمكن إضافتها إلى Media Library
            if ($image) {
                $biller->addMedia($image)->toMediaCollection('biller', 'billers_media');
            }

            if (!$this->sendMailAction->execute($data, BillerCreate::class)) {
                $message = __('User created successfully. Please setup your mail settings to send mail.');
            } else {
                $message = __('User created successfully.');
            }

            return $message;
        } catch (Exception $e) {
            Log::error("Error creating biller: " . $e->getMessage());
            throw new Exception("Operation failed: " . $e->getMessage());
        }
    }

    public function getBillerEditData($id)
    {
        $this->authorize('billers-edit');
        return Biller::findOrFail($id);
    }

    public function updateBiller($id, array $data)
    {
        $this->authorize('billers-edit');

        try {
            // التحقق مما إذا كان حقل الصورة موجودًا في البيانات
            $image = $data['image'] ?? null;

            // إزالة حقل 'image' فقط إذا كان موجودًا في المصفوفة
            if (isset($data['image'])) {
                unset($data['image']);
            }

            // تحديث بيانات الفواتير بدون الصورة
            $biller = Biller::findOrFail($id);
            $biller->update($data);

            // تحديث الصورة في Media Library إذا تم إرسال صورة جديدة
            if (!empty($image)) {
                // حذف الصورة القديمة إن وجدت
                $biller->clearMediaCollection('biller');

                // إضافة الصورة الجديدة
                $biller->addMedia($image)->toMediaCollection('biller', 'billers_media');
            }


            return __('Biller updated successfully.');
        } catch (Exception $e) {
            Log::error("Error updating biller: " . $e->getMessage());
            throw new Exception("Operation failed: " . $e->getMessage());
        }
    }

    public function deleteBillers(array $userIds)
    {
        DB::beginTransaction();

        try {
            $billers = Biller::whereIn('id', $userIds)->get(); // جلب السجلات قبل الحذف

            foreach ($billers as $biller) {
                $biller->clearMediaCollection('biller'); // حذف الصور من Media Library
            }

            Biller::whereIn('id', $userIds)->delete(); // حذف الفواتير بعد حذف الصور

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteBiller($id)
    {
        try {
            $biller = Biller::findOrFail($id);
            $biller->clearMediaCollection('biller');
            $biller->delete();

        } catch (\Exception $e) {
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function getTrashedBiller()
    {
        $this->authorize('billers-index');
        return Biller::onlyTrashed()->get();
    }

    public function restoreBiller($id)
    {
        $this->authorize('billers-index');

        try {
            $biller = Biller::withTrashed()->findOrFail($id);
            $biller->restore();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }

    }


}

