<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Tenant\CustomerService;
use App\Services\Tenant\DiscountPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscountPlanController extends Controller
{

    private DiscountPlanService $discountPlanService;
    private CustomerService $customerService;

    public function __construct(DiscountPlanService $discountPlanService,CustomerService $customerService)
    {
        $this->discountPlanService = $discountPlanService;
        $this->customerService = $customerService;
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
            $this->authorize('discount_plan');

            // Get discountPlan with optimized queries
            $lims_discount_plan_all = $this->discountPlanService->getDiscountPlan();

            return view('Tenant.discount_plan.index', compact('lims_discount_plan_all'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load discount Plan. Please try again.');
        }

    }

    public function create(): View|RedirectResponse
    {
        try {
            $lims_customer_list = $this->customerService->getCustomers();
            return view('Tenant.discount_plan.create', compact('lims_customer_list'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load discount Plan create. Please try again.');
        }
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            // Delegate the DiscountPlan creation to the holiday service.
            $holiday = $this->discountPlanService->createDiscountPlan($request->all());

            // Redirect to the DiscountPlan index page with a success message.
            return redirect()->route('discount-plans.index')->with('message', 'DiscountPlan created successfully');
        } catch (\Exception $e) {
            // In case of any error, redirect back with an error message.
            return redirect()->route('discount-plans.index')->with('not_permitted', 'Unexpected error occurred.');
        }
    }

    public function edit($id): View|RedirectResponse
    {
        try {
            $data = $this->discountPlanService->edit($id);
            return view('Tenant.discount_plan.edit', $data);
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load discount Plan edit. Please try again.');
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->discountPlanService->updateDiscountPlan($id, $request->all());
            return redirect()->route('discount-plans.index')->with('message', 'Discount Plan updated successfully');
        } catch (\Exception $e) {
            return back()->with(['not_permitted' => 'An error occurred while updating the discount plan.']);
        }
    }


}
