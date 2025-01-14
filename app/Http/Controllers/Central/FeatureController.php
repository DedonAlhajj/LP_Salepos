<?php

namespace App\Http\Controllers\Central;

use App\Exceptions\FeatureDeletionException;
use App\Exceptions\PackageException;
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
        try {
            $this->featureService->createFeature($request->validated());
            return redirect()->route('Central.packages.create')->with('message', 'Feature added');
        } catch (FeatureDeletionException $e) {
            return redirect()->route('Central.features.index')->with('message', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('Central.features.index')->with('message', 'خطأ غير متوقع: ' . $e->getMessage());
        }
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
        try {
            $this->featureService->updateFeature($feature, $request->validated());
            return redirect()->route('Central.features.index')->with('message', 'Feature updated');
        } catch (FeatureDeletionException $e) {
            return redirect()->route('Central.features.index')->with('message', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('Central.features.index')->with('message', 'خطأ غير متوقع: ' . $e->getMessage());
        }
    }

    /**
     * حذف الميزة.
     */
    public function destroy(Feature $feature)
    {
        try {
            $this->featureService->deleteFeature($feature);
            return redirect()->route('Central.features.index')->with('message', 'Feature Deleted.');
        } catch (FeatureDeletionException $e) {
            return redirect()->route('Central.features.index')->with('message', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('Central.features.index')->with('message', 'خطأ غير متوقع: ' . $e->getMessage());
        }
    }
}
