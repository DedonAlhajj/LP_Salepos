<?php

namespace App\Services\Central;

use App\Exceptions\FeatureDeletionException;
use App\Exceptions\PackageException;
use App\Models\Feature;
use Exception;
use Illuminate\Validation\ValidationException;

class FeatureService
{
    /**
     * استرجاع جميع الميزات.
     */
    public function getAllFeatures()
    {
        return Feature::all();
    }

    /**
     * إنشاء ميزة جديدة.
     */
    public function createFeature(array $data)
    {
        try {
            Feature::create($data);
        } catch (\Exception $e) {
            throw new FeatureDeletionException('some thing not right :' . $e->getMessage());
        }

    }

    /**
     * تحديث الميزة.
     */
    public function updateFeature(Feature $feature, array $data)
    {
        try {
            $feature->update($data);
        } catch (\Exception $e) {
            throw new FeatureDeletionException('some thing not right :' . $e->getMessage());
        }
    }

    /**
     * حذف الميزة.
     */
    public function deleteFeature(Feature $feature)
    {

        if ($feature->packages()->exists()) {
            throw new FeatureDeletionException('You can not delete this feature , some package are use it .');
        }

        try {
            $feature->delete();
        } catch (\Exception $e) {
            throw new FeatureDeletionException('some thing not right :' . $e->getMessage());
        }
    }
}
