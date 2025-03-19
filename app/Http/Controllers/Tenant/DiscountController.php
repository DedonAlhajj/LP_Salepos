<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreDiscountRequest;
use App\Services\Tenant\CustomerService;
use App\Services\Tenant\DiscountService;
use App\Services\Tenant\ProductSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\DiscountPlan;
use App\Models\Product;
use App\Models\DiscountPlanDiscount;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Auth;

class DiscountController extends Controller
{

    private DiscountService $discountService;
    private ProductSearchService $productSearchService;

    public function __construct(DiscountService $discountService,ProductSearchService $productSearchService)
    {
        $this->discountService = $discountService;
        $this->productSearchService = $productSearchService;
    }

    /**
     * Display a listing of discounts.
     *
     * Retrieves all available discounts and returns the index view.
     * Handles errors gracefully by redirecting back with an error message if retrieval fails.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Check authorization for managing discounts
            $this->authorize('discount_plan');

            // Retrieve all discount records using the optimized service method
            $lims_discount_all = $this->discountService->getDiscount();

            return view('Tenant.discount.index', compact('lims_discount_all'));
        } catch (\Exception $e) {
            // Return an error message if fetching discounts fails
            return back()->with('not_permitted', 'Failed to load discount. Please try again.');
        }
    }

    /**
     * Show the form for creating a new discount.
     *
     * Retrieves necessary data for creating a discount.
     * Redirects back with an error message if the data retrieval fails.
     *
     * @throws \Exception
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        try {
            // Fetch discount plans required for the creation form
            $lims_discount_plan_list = $this->discountService->create();

            return view('Tenant.discount.create', compact('lims_discount_plan_list'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load discount create. Please try again.');
        }
    }

    /**
     * Search for a product by its code.
     *
     * Fetches a product based on the provided code and returns essential product details.
     *
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function productSearch(string $code): \Illuminate\Http\JsonResponse
    {
        try {
            // Pass the selected product IDs to the service for deletion.
            $product = $this->productSearchService->productSearch($code);

            // Return a success message in the response.
            return response()->json($product);
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed fetching.
            return response()->json('Failed to fetching product!');
        }
    }

    /**
     * Store a newly created discount in the database.
     *
     * Uses a validated request to ensure data integrity before saving.
     * Returns a success message or an error message in case of failure.
     *
     * @param StoreDiscountRequest $request
     * @return RedirectResponse
     */
    public function store(StoreDiscountRequest $request): RedirectResponse
    {
        try {
            // Create discount using service layer
            $this->discountService->createDiscount($request->validated());

            return redirect()->route('discounts.index')
                ->with('message', 'Discount created successfully');
        } catch (\Exception $e) {
            return back()->withErrors([
                'not_permitted' => 'An error occurred while creating the discount.'
            ]);
        }
    }

    /**
     * Show the form for editing a specific discount.
     *
     * Retrieves the discount data required for editing.
     * Redirects back with an error message if retrieval fails.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            // Fetch discount data for editing
            $data = $this->discountService->edit($id);

            return view('Tenant.discount.edit', $data);
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load discount edit. Please try again.');
        }
    }

    /**
     * Update the specified discount in the database.
     *
     * Uses a validated request to update an existing discount.
     * Handles exceptions and provides user-friendly error messages.
     *
     * @param StoreDiscountRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(StoreDiscountRequest $request, int $id): RedirectResponse
    {
        try {
            // Update discount using service layer
            $this->discountService->updateDiscount($id, $request->validated());

            return redirect()->route('discounts.index')
                ->with('message', 'Discount updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('discounts.index')
                ->with('error', 'An error occurred while updating the discount. Please try again later.');
        }
    }



}
