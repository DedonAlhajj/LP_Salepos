<?php

namespace App\Services\Central;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageService
{
    /**
     * استرجاع جميع الحزم.
     */
    public function getAllPackages()
    {
        return Package::with('features')->where('is_active', '1')->get();
    }

    /**
     * إنشاء حزمة جديدة.
     */
    public function createPackage(array $data)
    {
        $package = Package::create($data);

        // إذا تم إرسال ميزات، قم بربطها بالحزمة
        if (isset($data['features'])) {
            $package->features()->sync($data['features']);
        }

        return $package;
        //return Package::create($data);
    }

    /**
     * تحديث الحزمة.
     */
    public function updatePackage(Package $package, array $data)
    {
        $package->update($data);

        // تحديث الربط مع الميزات
        if (isset($data['features'])) {
            $package->features()->sync($data['features']);
        }
        //$package->update($data);
    }

    /**
     * حذف الحزمة.
     */
    public function deletePackage(Package $package)
    {
        $package->delete();
    }
}
