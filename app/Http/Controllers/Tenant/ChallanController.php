<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\ChallanStoreDTO;
use App\DTOs\ChallanUpdateDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\ChallanService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChallanController extends Controller
{

    /**
     * @var ChallanService
     */
    private $challanService;

    /**
     * ChallanController constructor.
     *
     * @param ChallanService $challanService - Injected service handling business logic.
     */
    public function __construct(ChallanService $challanService)
    {
        $this->challanService = $challanService;
    }

    /**
     * Display a list of Challans based on optional filters.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            // Retrieve filters from request, with default values
            $courier_id = $request->input('courier_id', 'All Courier');
            $status = $request->input('status', null);

            // Fetch data using the service
            $data = $this->challanService->getIndexData($courier_id, $status);

            // Render the index view with retrieved data
            return view('Tenant.challan.index', $data);
        } catch (Exception $e) {
            // Handle errors and redirect back with an error message
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }

    /**
     * Show the form for creating a new Challan.
     *
     * @param Request $request
     * @return
     */
    public function create(Request $request)
    {
        try {
            // Retrieve required data for the creation page
            $data = $this->challanService->getCreateDate($request->packing_slip_id);

            // Load the create view with relevant data
            return view('Tenant.challan.create', $data);
        } catch (Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }

    /**
     * Store a newly created Challan in the database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Convert request data into a DTO for better structure
            $challanDTO = ChallanStoreDTO::fromRequest($request);

            // Pass DTO to the service for processing
            $this->challanService->createChallan($challanDTO);

            // Redirect to index page with success message
            return redirect()->route('challan.index')->with('message', 'Challan created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }

    /**
     * Generate an invoice for a specific Challan.
     *
     * @param int $id - Challan ID
     * @return View|RedirectResponse
     */
    public function genInvoice(int $id): View|RedirectResponse
    {
        try {
            // Fetch invoice data for the given challan ID
            $data = $this->challanService->getInvoiceData($id);

            // Render the invoice view with relevant data
            return view('Tenant.challan.invoice', $data);
        } catch (Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }

    /**
     * Finalize a Challan, confirming its completion.
     *
     * @param int $id - Challan ID
     * @return View|RedirectResponse
     */
    public function finalize($id): View|RedirectResponse
    {
        try {
            // Retrieve the specific Challan for finalization
            $challan = $this->challanService->getFindChallan($id);

            // Render the finalization page with Challan data
            return view('Tenant.challan.finalize', compact('challan'));
        } catch (Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }

    /**
     * Update an existing Challan with new data.
     *
     * @param Request $request
     * @param int $id - Challan ID
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            // Merge request data with Challan ID into DTO for structured processing
            $dto = new ChallanUpdateDTO(array_merge($request->all(), ['challan_id' => $id]));

            // Pass DTO to service for updating the Challan
            $this->challanService->updateChallan($dto);

            return redirect()->route('challan.index')->with('message', 'Challan updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }

    /**
     * Generate a money receipt for a specific Challan.
     *
     * @param int $id - Challan ID
     * @return View|RedirectResponse
     */
    public function moneyReciept($id): View|RedirectResponse
    {
        try {
            // Retrieve Challan data for the receipt
            $challan = $this->challanService->getFindChallan($id);

            // Render the money receipt view with Challan data
            return view('Tenant.challan.money_reciept', compact('challan'));
        } catch (Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage() . ', please try again.');
        }
    }
}
