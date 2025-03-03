<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\QuotationDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\CustomerService;
use App\Services\Tenant\ProductVariantService;
use App\Services\Tenant\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{

    protected QuotationService $quotationService;
    protected CustomerService $customerService;
    protected ProductVariantService $productVariantService;

    public function __construct(
        QuotationService $quotationService,
        CustomerService $customerService,
        ProductVariantService $productVariantService)
    {
        $this->quotationService = $quotationService;
        $this->customerService = $customerService;
        $this->productVariantService = $productVariantService;
    }

    public function index(Request $request)
    {

        try {
        $data = $this->quotationService->getFilters($request);
        $quotations = $this->quotationService->getQuotations($data);

        return view('Tenant.quotation.index', compact('data', 'quotations'));

        } catch (\Exception $e) {
            Log::error("Error in quotation index: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while store Expense. Try again.');
        }
    }

    public function productQuotationData($id)
    {
        try {
            $products = $this->quotationService->productQuotationData($id);
            return response()->json([
                'products' => $products
            ]);
        } catch (\Exception $e) {
            Log::error("Error in productQuotationData: " . $e->getMessage());
            return "Error";
        }

    }

    public function create()
    {
        try {
            $data = $this->quotationService->getCreateQuotationData();
            return view('Tenant.quotation.create', $data);

        } catch (\Exception $e) {
            Log::error("Error in quotation create: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while quotation create. Try again.');
        }
    }

    public function getProduct($id)
    {
        try {
            return response()->json($this->quotationService->getProduct($id));
        } catch (\Exception $e) {
            return response()->json("Error");
        }
    }

    public function getCustomerGroup($id)
    {
        try {
            return response()->json($this->customerService->getCustomerGroup($id));
        } catch (\Exception $e) {
            return response()->json("Error");
        }
    }

    public function limsProductSearch(Request $request)
    {
        try {
            return response()->json($this->productVariantService->limsProductSearch($request));
        } catch (\Exception $e) {
            return response()->json("Error");
        }
    }

    public function store(Request $request)
    {
        try {
            $message = $this->quotationService->createQuotation($request->all());
            return redirect('quotations')->with('message', $message['message']);

        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while quotation store. Try again.');
        }
    }

    public function sendMail(Request $request)
    {
        try {
            $message = $this->quotationService->sendMail($request->input('quotation_id'));
            return redirect()->back()->with('message', $message);

        } catch (\Exception $e) {
            Log::error("Error in quotation sendMail: " . $e->getMessage());
            return redirect()->back()->with('message', "Error send Mail");
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->quotationService->getEditData($id);
            return view('Tenant.quotation.edit', $data);

        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while quotation store. Try again.');
        }
    }

    public function update(Request $request,$id)
    {
        try {
            $dto = QuotationDTO::fromRequest($request);
            $message = $this->quotationService->updateQuotation($id, $dto);
            return redirect()->route('quotations.index')
                ->with('message', __($message));

        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while quotation update. Try again.');
        }
    }

    public function createSale($id)
    {
        try {
            $data = $this->quotationService->getCreateSaleData($id);
            return view('Tenant.quotation.create_sale', $data);
        } catch (\Exception $e) {
            Log::error("Error in quotation createSale: " . $e->getMessage());
            return redirect()->back()->with('message', "Error fetching Data");
        }
    }

    public function createPurchase($id)
    {
        try {
            $data = $this->quotationService->getCreatePurchaseData($id);
            return view('Tenant.quotation.create_purchase', $data);
        } catch (\Exception $e) {
            Log::error("Error in quotation createPurchase: " . $e->getMessage());
            return redirect()->back()->with('message', "Error fetching Data");
        }
    }

    public function deleteBySelection(Request $request)
    {
        $quotationIds = $request->input('quotationIdArray', []);

        if (empty($quotationIds)) {
            return response()->json(['message' => 'No quotations selected'], 400);
        }

        $deleted = $this->quotationService->deleteQuotations($quotationIds);

        return response()->json([
             $deleted ? 'Quotations deleted successfully!' : 'Failed to delete quotations'], $deleted ? 200 : 500);
    }

    public function destroy($id)
    {
        $deleted = $this->quotationService->deleteQuotationById($id);

        return redirect('quotations')->with(
            'message',
            $deleted ? 'Quotation deleted successfully' : 'Failed to delete quotation'
        );
    }
}
