<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CustomFieldRequest;
use App\Services\Tenant\CustomFieldService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CustomFieldController extends Controller
{
    private CustomFieldService $customFieldService;

    public function __construct(CustomFieldService $customFieldService)
    {
        $this->customFieldService = $customFieldService;
    }

    /**
     * Display a listing of holidays.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Check authorization efficiently
            $this->authorize('custom_field');

            // Get holidays with optimized queries
            $custom_field_all = $this->customFieldService->getAllCustomFields();

            return view('Tenant.custom_field.index', compact('custom_field_all'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load holidays. Please try again.');
        }
    }

    public function create(): View
    {
        // Check authorization efficiently
        $this->authorize('custom_field');

        return view('Tenant.custom_field.create');
    }

    public function store(CustomFieldRequest $request): RedirectResponse
    {
        // التحقق من وضع العرض التجريبي
        if (config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            $customField = $this->customFieldService->createCustomField($request->validated());

            return redirect()->route('custom-fields.index')->with('message', 'Custom Field created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['not_permitted' => 'An error occurred while creating the custom field.']);
        }
    }

    public function edit($id)
    {
        // Check authorization efficiently
        $this->authorize('custom_field');
        $custom_field_data = $this->customFieldService->findCustomFieldById($id);

        return view('Tenant.custom_field.edit', compact('custom_field_data'));
    }

    public function update(CustomFieldRequest $request, $id): RedirectResponse
    {
        // التحقق من وضع العرض التجريبي
        if (config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            $customField = $this->customFieldService->updateBasicCustomField($request->validated(),$id);

            return redirect()->route('custom-fields.index')->with('message', 'Custom Field updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['not_permitted' => 'An error occurred while updating the custom field.']);
        }
    }

    public function destroy($id)
    {
        // التحقق من وضع العرض التجريبي
        if (config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            $customField = $this->customFieldService->deleteCustomField($id);

            return redirect()->route('custom-fields.index')->with('message', 'Custom Field deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['not_permitted' => 'An error occurred while deleting the custom field.']);
        }
    }
}
