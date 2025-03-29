<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\DeliveryDTO;
use App\DTOs\DeliveryEditDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\CourierServices;
use App\Services\Tenant\DeliveryService;
use App\Services\Tenant\MailService;
use App\Services\Tenant\ProductDeliveryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{

    protected DeliveryService $deliveryService;
    protected CourierServices $courierServices;
    protected ProductDeliveryService $productDeliveryService;
    protected MailService $mailService;

    public function __construct(
        DeliveryService $deliveryService,
        CourierServices $courierServices,
        ProductDeliveryService $productDeliveryService,
        MailService $mailService
    )
    {
        $this->deliveryService = $deliveryService;
        $this->courierServices = $courierServices;
        $this->productDeliveryService = $productDeliveryService;
        $this->mailService = $mailService;
    }

    /**
     * Controller class for handling delivery-related operations.
     * This class contains various methods for managing deliveries,
     * including listing deliveries, fetching data, and sending emails.
     */

    public function index(): View|RedirectResponse
    {
        try {
            // Authorize user for the 'delivery' permission.
            $this->authorize('delivery');

            // Retrieve all delivery data from the Delivery Service.
            $deliveries = $this->deliveryService->getAllDeliveries();

            // Retrieve the list of courier companies from the Courier Service.
            $couriers = $this->courierServices->getCourier();

            // Return the deliveries and couriers data to the index view.
            return view('Tenant.delivery.index', compact('deliveries', 'couriers'));
        } catch (\Exception $e) {
            // If an exception occurs, redirect back with an error message.
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }

    /**
     * Fetches detailed product delivery data based on the given product ID.
     * Returns the data as a JSON response.
     *
     * @param int $id The product ID.
     * @return JsonResponse
     */
    public function productDeliveryData(int $id): JsonResponse
    {
        try {
            // Retrieve the product delivery data from the Product Delivery Service.
            $productData = $this->productDeliveryService->getProductDeliveryData($id);

            // Return a JSON response with the fetched data and success status.
            return response()->json([
                'success' => true,
                'data' => $productData,
            ]);
        } catch (\Exception $e) {
            // Return a JSON response with the error message and failure status.
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retrieves the details required to create a new delivery for the given ID.
     *
     * @param int $id The delivery ID.
     * @return JsonResponse
     */
    public function create(int $id): JsonResponse
    {
        try {
            // Fetch delivery creation details from the Delivery Service.
            $deliveryData = $this->deliveryService->getDeliveryDetailsCreate($id);

            // Return the data as a JSON response.
            return response()->json($deliveryData);
        } catch (\Exception $e) {
            // Return an error message and HTTP 500 status code in case of failure.
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Stores a new delivery using data from the incoming request.
     * Redirects to the delivery page upon success or failure.
     *
     * @param Request $request The HTTP request containing delivery details.
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Create a DeliveryDTO object from the request data.
            $dto = DeliveryDTO::fromRequest($request);

            // Store the new delivery via the Delivery Service.
            $this->deliveryService->storeDelivery($dto);

            // Redirect to the delivery page with a success message.
            return redirect('delivery')->with('message', 'Delivery created successfully');
        } catch (\Exception $e) {
            // Redirect to the delivery page with an error message in case of failure.
            return redirect('delivery')->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Sends an email related to a specific delivery based on the provided request.
     * Redirects back to the previous page with a message indicating the result.
     *
     * @param Request $request The HTTP request containing the delivery ID.
     * @return RedirectResponse
     */
    public function sendMail(Request $request): RedirectResponse
    {
        try {
            // Send the delivery email using the Mail Service.
            $message = $this->mailService->sendDeliveryMail((int) $request->input('delivery_id'));

            // Redirect back with a success message.
            return redirect()->back()->with('message', $message);
        } catch (\Exception $e) {
            // Redirect back with an error message in case of failure.
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Fetches and returns the details of a specific delivery by its ID.
     * The details are returned as a JSON response.
     *
     * @param int $id The ID of the delivery to be fetched.
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        try {
            // Retrieve delivery details using the Delivery Service for the given ID.
            $deliveryData = $this->deliveryService->getDeliveryDetails($id);

            // Return the delivery details as a JSON response.
            return response()->json($deliveryData);
        } catch (\Exception $e) {
            // In case of an exception, return an error message and HTTP 500 status code.
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Updates the details of an existing delivery based on the provided request data.
     * Redirects to the delivery page with the operation status.
     *
     * @param Request $request The HTTP request containing updated delivery details.
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            // Convert request data to a DeliveryEditDTO object.
            $dto = DeliveryEditDTO::fromRequest($request);

            // Perform the update operation using the Delivery Service.
            $message = $this->deliveryService->updateDelivery($dto);

            // Redirect to the delivery page with a success message.
            return redirect('delivery')->with('message', $message);
        } catch (\Exception $e) {
            // Redirect to the delivery page with an error message if the update fails.
            return redirect('delivery')->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Deletes multiple deliveries based on the provided selection array.
     * Returns a JSON response indicating the operation's success or failure.
     *
     * @param Request $request The HTTP request containing an array of delivery IDs to delete.
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Delete deliveries based on the input array of delivery IDs from the request.
            $this->deliveryService->deleteDelivers($request->input('deliveryIdArray'));

            // Return a success message as a JSON response.
            return response()->json('Delivery deleted successfully!');
        } catch (\Exception $e) {
            // Return an error message as a JSON response if deletion fails.
            return response()->json('Failed to delete Delivery!');
        }
    }

    /**
     * Deletes a specific delivery by its ID.
     * Redirects back with a message indicating the success or failure of the operation.
     *
     * @param int $id The ID of the delivery to be deleted.
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        try {
            // Delete the delivery with the specified ID using the Delivery Service.
            $this->deliveryService->deleteDelivery($id);

            // Redirect back to the previous page with a success message.
            return redirect()->back()->with('message', 'Delivery deleted successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if deletion fails.
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }

}
