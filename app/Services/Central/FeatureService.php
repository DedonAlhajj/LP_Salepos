<?php

namespace App\Services\Central;

use App\Models\Feature;

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
        return Feature::create($data);
    }

    /**
     * تحديث الميزة.
     */
    public function updateFeature(Feature $feature, array $data)
    {
        $feature->update($data);
    }

    /**
     * حذف الميزة.
     */
    public function deleteFeature(Feature $feature)
    {
        $feature->delete();
    }
}
