<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Tenant\CourierServices;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourierController extends Controller
{

    protected CourierServices $courierServices;

    public function __construct(CourierServices $courierServices)
    {
        $this->courierServices = $courierServices;
    }

    /**
     * Display the courier index page.
     *
     * This method ensures the user is authorized before retrieving all courier
     * data via the service layer and returning the index view.
     *
     * @return View|RedirectResponse The courier index view or a redirect response.
     * @throws Exception If an error occurs during retrieval.
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Retrieve all courier data from the service layer
            $couriers = $this->courierServices->getCourier();

            // Return the index view with the retrieved data
            return view('Tenant.courier.index', compact('couriers'));
        } catch (Exception $e) {
            // Redirect back with an error message if an exception occurs
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }


    /**
     * Store a newly created courier.
     *
     * This method converts request data into a DTO and passes it to the service layer
     * for courier creation.
     *
     * @param Request $request The request containing courier details.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If courier creation fails.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Create the courier using the service layer
            $this->courierServices->createCourier($request->all());

            // Redirect to the index page with a success message
            return redirect()->back()->with('message', 'courier created successfully');
        } catch (Exception $e) {
            // Redirect back with an error message if an exception occurs
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }


    /**
     * Update an existing courier.
     *
     * This method validates incoming request data, converts it into a DTO,
     * and passes it to the service layer for updating the courier details.
     *
     * @param Request $request The request containing updated courier details.
     * @param int $id The ID of the courier to be updated.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If the courier update fails.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Update the courier using the service layer
            $this->courierServices->updateCourier($request->all());

            // Redirect to the courier list with a success message
            return redirect()->back()->with('message', 'courier updated successfully');
        } catch (ModelNotFoundException $exception) {
            // Handle case where the courier is not found
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        } catch (Exception $exception) {
            // Handle general exceptions during update
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }


    /**
     * Delete a specific Courier by its ID.
     *
     * This method calls the service layer to delete the Courier and redirects back
     * with a success or failure message.
     *
     * @param int $id The ID of the Courier to be deleted.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If deletion fails.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service layer to delete the specified Courier
            $this->courierServices->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Courier deleted successfully');
        } catch (ModelNotFoundException $exception) {
            // Handle case where the Courier is not found
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (Exception $e) {
            // Handle general exceptions during deletion
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }
}
