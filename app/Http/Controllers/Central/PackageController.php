<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Package;
use App\Http\Requests\Central\PackageRequest;
use App\Services\Central\PackageService;


class PackageController extends Controller
{
    protected $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * عرض قائمة الحزم المتاحة.
     */
    public function index()
    {
        $packages = $this->packageService->getAllPackages();
        return view('Central.packages.index', compact('packages'));
    }

    /**
     * عرض تفاصيل الحزمة.
     */
    public function show(Package $package)
    {
        $package->load('features');
       //return $package;
        return view('Central.packages.show', compact('package'));
    }

    /**
     * عرض صفحة إنشاء حزمة جديدة.
     */
    public function create()
    {
        $features = Feature::all();
        return view('Central.packages.create',compact('features'));
    }

    /**
     * تخزين حزمة جديدة.
     */
    public function store(PackageRequest $request)
    {
        $package = $this->packageService->createPackage($request->validated());
        return redirect()->route('Central.packages.index')->with('success', 'تم إضافة الحزمة بنجاح');
    }

    /**
     * عرض صفحة تعديل الحزمة.
     */
    public function edit(Package $package)
    {
        $features = $package->features;
        return view('Central.packages.edit', compact('features','package'));
        
    }

    /**
     * تحديث الحزمة.
     */
    public function update(PackageRequest $request, Package $package)
    {
        $this->packageService->updatePackage($package, $request->validated());
        return redirect()->route('Central.packages.index')->with('success', 'تم تحديث الحزمة بنجاح');
    }

    /**
     * حذف الحزمة.
     */
    public function destroy(Package $package)
    {
        $this->packageService->deletePackage($package);
        return redirect()->route('Central.packages.index')->with('success', 'تم حذف الحزمة بنجاح');
    }
}
