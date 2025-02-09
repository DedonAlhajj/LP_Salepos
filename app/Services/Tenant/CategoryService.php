<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\Mail\BillerCreate;
use App\Mail\UserDetails;
use App\Models\Biller;
use App\Models\Category;
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
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CategoryService
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

    public function getAllCategoriesWithData()
    {
        $this->authorize('category');

        $categories = Category::with(['parent', 'products' => function ($query) {
            $query->select('id', 'category_id', 'qty', 'price', 'cost');
        }])->withTrashed()->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'parent_name' => optional($category->parent)->name,

                'image' => str_replace(config('app.url'), request()->getSchemeAndHttpHost(), $category->getFirstMediaUrl('category_images')) ?: asset('images/zummXD2dvAtI.png'),
                'product_count' => $category->products->count(),
                'total_stock' => $category->products->sum('qty'),
                'total_price' => $category->products->sum(fn($p) => $p->price * $p->qty),
                'total_cost' => $category->products->sum(fn($p) => $p->cost * $p->qty),
            ];
        });
        return $categories;
    }

    public function createCategory(array $data)
    {
        try {

            $category = new Category();
            $category->name = $data['name'];
            $category->slug = Str::slug($data['name'], '-');
            $category->parent_id = $data['parent_id'] ?? null;
            $category->is_sync_disable = $data['is_sync_disable'] ?? false;

            if ($this->isEcommerceEnabled()) {
                $category->short_description = $data['short_description'] ?? null;
                $category->page_title = $data['page_title'] ?? null;
                $category->featured = $data['featured'] ?? false;
            }


            // استخدام `InteractsWithMedia` لرفع الصور
            if (isset($data['image'])) {
                $category->addMedia($data['image'])->toMediaCollection('category_images');
            }
            if (isset($data['icon'])) {
                $category->addMedia($data['icon'])->toMediaCollection('category_icons');
            }

            $category->save();
            return $category;
        } catch (\Exception $e) {
            Log::error('Error while deleting the category: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    private function isEcommerceEnabled()
    {
        return in_array('ecommerce', explode(',', config('addons')));
    }

    public function updateCategory(Category $category, array $data)
    {
        try {
            // تحديث البيانات النصية
            $category->name = $data['name'] ?? $category->name;
            $category->slug = Str::slug($category->name, '-');
            $category->parent_id = $data['parent_id'] ?? $category->parent_id;
            $category->is_sync_disable = $data['is_sync_disable'] ?? $category->is_sync_disable;

            if ($this->isEcommerceEnabled()) {
                $category->short_description = $data['short_description'] ?? $category->short_description;
                $category->page_title = $data['page_title'] ?? $category->page_title;
                $category->featured = $data['featured'] ?? $category->featured;
            }

            // تحديث الصورة (إذا تم توفير صورة جديدة)
            if (isset($data['image'])) {
                $category->clearMediaCollection('category_images'); // حذف الصورة القديمة
                $category->addMedia($data['image'])->toMediaCollection('category_images');
            }

            // تحديث الأيقونة (إذا تم توفير أيقونة جديدة)
            if (isset($data['icon'])) {
                $category->clearMediaCollection('category_icons'); // حذف الأيقونة القديمة
                $category->addMedia($data['icon'])->toMediaCollection('category_icons');
            }

            $category->save();
            return $category;
        } catch (\Exception $e) {
            Log::error('Error while updating the category: ' . $e->getMessage());
            throw new Exception("Operation failed: " . $e->getMessage());
        }
    }

    public function deleteCategories(array $userIds)
    {
        DB::beginTransaction();

        try {
            $categorys = Category::whereIn('id', $userIds)->get(); // جلب السجلات قبل الحذف

            foreach ($categorys as $category) {
                $category->clearMediaCollection('category_images'); // حذف الصور من Media Library
                $category->clearMediaCollection('category_icons');
            }

            Category::whereIn('id', $userIds)->delete(); // حذف الفواتير بعد حذف الصور

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->clearMediaCollection('category_images'); // حذف الصور من Media Library
            $category->clearMediaCollection('category_icons');
            $category->delete();

        } catch (\Exception $e) {
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }



}

