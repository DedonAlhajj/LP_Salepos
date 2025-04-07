<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\CouponDTO;
use App\DTOs\CouponUpdateDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\CouponService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Display the coupon index page.
     *
     * This method ensures the user is authorized before retrieving all coupon
     * data via the service layer and returning the index view.
     *
     * @return View|RedirectResponse The coupon index view or a redirect response.
     * @throws Exception If an error occurs during retrieval.
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Ensure the user has permission to access coupons
            $this->authorize('unit');

            // Retrieve all coupon data from the service layer
            $coupons = $this->couponService->getIndexData();

            // Return the index view with the retrieved data
            return view('Tenant.coupon.index', compact('coupons'));
        } catch (Exception $e) {
            // Redirect back with an error message if an exception occurs
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }

    /**
     * Store a newly created coupon.
     *
     * This method converts request data into a DTO and passes it to the service layer
     * for coupon creation.
     *
     * @param Request $request The request containing coupon details.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If coupon creation fails.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Convert request data into a DTO
            $dto = CouponDTO::fromRequest($request);

            // Create the coupon using the service layer
            $this->couponService->createCoupon($dto);

            // Redirect to the index page with a success message
            return redirect()->route('coupons.index')->with('message', 'Coupon created successfully');
        } catch (Exception $e) {
            // Redirect back with an error message if an exception occurs
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }

    /**
     * Update an existing coupon.
     *
     * This method validates incoming request data, converts it into a DTO,
     * and passes it to the service layer for updating the coupon details.
     *
     * @param Request $request The request containing updated coupon details.
     * @param int $id The ID of the coupon to be updated.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If the coupon update fails.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Convert request data into a DTO
            $dto = CouponUpdateDTO::fromRequest($request);

            // Update the coupon using the service layer
            $this->couponService->updateCoupon($dto);

            // Redirect to the coupon list with a success message
            return redirect('coupons')->with('message', 'Coupon updated successfully');
        } catch (ModelNotFoundException $exception) {
            // Handle case where the coupon is not found
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        } catch (Exception $exception) {
            // Handle general exceptions during update
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }

    /**
     * Generate a unique Coupon code.
     *
     * This method utilizes the service layer to generate a unique Coupon code
     * and returns the result as a JSON response.
     *
     * @return JsonResponse JSON response containing the generated code.
     * @throws Exception If code generation fails.
     */
    public function generateCode(): JsonResponse
    {
        try {
            // Generate a unique Coupon code
            $id = $this->couponService->generateCode();

            // Return the generated code in a JSON response
            return response()->json($id);
        } catch (Exception $e) {
            // Handle error gracefully
            return response()->json("Error generating Coupon code");
        }
    }

    /**
     * Delete multiple Coupons selected by the user.
     *
     * This method passes the selected Coupon IDs to the service layer for batch deletion.
     *
     * @param Request $request The request containing selected Coupon IDs.
     * @return JsonResponse JSON response indicating success or failure.
     * @throws Exception If deletion fails.
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Delete the selected Coupons using the service layer
            $this->couponService->deleteCoupon($request->input('couponIdArray'));

            // Return a success response
            return response()->json('Coupon deleted successfully!');
        } catch (ModelNotFoundException $exception) {
            // Handle case where a Coupon is not found
            return response()->json($exception->getMessage());
        } catch (Exception $e) {
            // Handle general exceptions during deletion
            return response()->json($e->getMessage());
        }
    }

    /**
     * Delete a specific Coupon by its ID.
     *
     * This method calls the service layer to delete the Coupon and redirects back
     * with a success or failure message.
     *
     * @param int $id The ID of the Coupon to be deleted.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If deletion fails.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service layer to delete the specified Coupon
            $this->couponService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Coupon deleted successfully');
        } catch (ModelNotFoundException $exception) {
            // Handle case where the Coupon is not found
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (Exception $e) {
            // Handle general exceptions during deletion
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }

}
