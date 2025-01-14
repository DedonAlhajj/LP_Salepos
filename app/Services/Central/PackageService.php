<?php

namespace App\Services\Central;

use App\Exceptions\PackageException;
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
    public function createPackage($data)
    {
        try {
            $package = Package::create([
                'package_name' => $data['package_name'],
                'duration' => $data['duration'],
                'duration_unit' => $data['duration_unit'],
                'price' => $data['price'],
                'description' => $data['description'],
                'max_users' => $data['max_users'],
                'max_storage' => $data['max_storage'],
                'is_active' => $data['is_active']  ?? '0',
                'is_trial' => $data['is_trial']  ?? '0',
            ]);

            if (isset($data['features'])) {
                $package->features()->sync($data['features']);
            }
        } catch (\Exception $e) {
            throw new PackageException('some thing not right :' . $e->getMessage());
        }
    }

    /**
     * تحديث الحزمة.
     */
    public function updatePackage(Package $package, array $data)
    {

        try {
            $package->update($data);
            // تحديث الربط مع الميزات
            if (isset($data['features'])) {
                $package->features()->sync($data['features']);
            }
        } catch (\Exception $e) {
            throw new PackageException('some thing not right :' . $e->getMessage());
        }

    }

    /**
     * حذف الحزمة.
     */
    public function deletePackage(Package $package)
    {
        try {
            $package->delete();
        } catch (\Exception $e) {
            throw new PackageException('some thing not right :' . $e->getMessage());
        }

    }
}
