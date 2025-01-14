<?php

namespace App\Http\Controllers\Central;

use App\Exceptions\PackageException;
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
        try {
            $this->packageService->createPackage($request->validated());
            return redirect()->route('Central.packages.index')->with('success', 'Package added');
        } catch (PackageException $e) {
            return redirect()->route('Central.features.index')->with('message', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('Central.features.index')->with('message', 'خطأ غير متوقع: ' . $e->getMessage());
        }

    }

    /**
     * عرض صفحة تعديل الحزمة.
     */
    public function edit(Package $package)
    {
        $features = Feature::all();
        return view('Central.packages.edit', compact('features','package'));

    }

    /**
     * تحديث الحزمة.
     */
    public function update(PackageRequest $request, Package $package)
    {
        try {
            $this->packageService->updatePackage($package, $request->validated());
            return redirect()->route('Central.packages.index')->with('success', 'Package Updated');
        } catch (PackageException $e) {
            return redirect()->route('Central.features.index')->with('message', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('Central.features.index')->with('message', 'خطأ غير متوقع: ' . $e->getMessage());
        }
    }

    /**
     * حذف الحزمة.
     */
    public function destroy(Package $package)
    {
        try {
            $this->packageService->deletePackage($package);
            return redirect()->route('Central.packages.index')->with('success', 'Package Deleted');
        } catch (PackageException $e) {
            return redirect()->route('Central.features.index')->with('message', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('Central.features.index')->with('message', 'خطأ غير متوقع: ' . $e->getMessage());
        }
    }
}
