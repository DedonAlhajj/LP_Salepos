<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Http\Requests\Central\FeatureRequest;
use App\Services\Central\FeatureService;

class FeatureController extends Controller
{
    protected $featureService;

    public function __construct(FeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    /**
     * عرض قائمة الميزات المتاحة.
     */
    public function index()
    {
        $features = $this->featureService->getAllFeatures();
        return view('Central.features.index', compact('features'));
    }

    /**
     * عرض تفاصيل ميزة.
     */
    public function show(Feature $feature)
    {
        return view('features.show', compact('feature'));
    }

    /**
     * عرض صفحة إضافة ميزة جديدة.
     */
    public function create()
    {
        return view('features.create');
    }

    /**
     * تخزين ميزة جديدة.
     */
    public function store(FeatureRequest $request)
    {
        $feature = $this->featureService->createFeature($request->validated());
        return redirect()->route('features.index')->with('success', 'تم إضافة الميزة بنجاح');
    }

    /**
     * عرض صفحة تعديل الميزة.
     */
    public function edit(Feature $feature)
    {
        return view('Central.features.edit', compact('feature'));
    }

    /**
     * تحديث الميزة.
     */
    public function update(FeatureRequest $request, Feature $feature)
    {
        $this->featureService->updateFeature($feature, $request->validated());
        return redirect()->route('Central.features.index')->with('success', 'تم تحديث الميزة بنجاح');
    }

    /**
     * حذف الميزة.
     */
    public function destroy(Feature $feature)
    {
        $this->featureService->deleteFeature($feature);
        return redirect()->route('Central.features.index')->with('success', 'تم حذف الميزة بنجاح');
    }
}
