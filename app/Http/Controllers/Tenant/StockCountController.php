<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Tenant\StockCountService;
use Illuminate\Http\Request;
use App\Models\StockCount;
use Illuminate\Support\Facades\Log;

class StockCountController extends Controller
{

    protected $stockCountService;

    public function __construct(StockCountService $stockCountService)
    {
        $this->stockCountService = $stockCountService;

    }
    public function index()
    {
        try {
            $data = $this->stockCountService->index();

            return view('Tenant.stock_count.index',$data);

        } catch (\Exception $e) {
            Log::error("Error fetching modifications: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error fetching modifications");
        }

    }
//hi how are you my name bassam alhajj and I work as ITE engineer in iwings company and I' writing a random text to annoy my sister
    public function store(Request $request)
    {
        $response = $this->stockCountService->storeStockCount($request->all());
        return redirect()->back()->with($response['status'] ? 'message' : 'not_permitted', $response['message']);
    }

    public function finalize(Request $request)
    {
        // ✅ Check if the file is present in the request
        if (!$request->hasFile('final_file')) {
            return redirect()->back()->with('not_permitted', 'No file uploaded.');
        }

        $result = $this->stockCountService->finalizeStockCount(
            $request->stock_count_id,
            $request->file('final_file')
        );

        return redirect()->back()->with($result['status'] ? 'message' : 'not_permitted', $result['message']);
    }

    public function stockDif($id)
    {
        try {
            $data = $this->stockCountService->stockDif($id);
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data.', 'details' => $e->getMessage()], 500);
        }

    }

    public function qtyAdjustment($id)
    {
        try {
            $data = $this->stockCountService->qtyAdjustment($id);
            if (!$data['status']){
                return redirect()->back()->with('not_permitted', $data['data']);
            }
            return view('Tenant.stock_count.qty_adjustment',$data['data']);

        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', $data['data']);
        }
    }

    public function downloadInitialFile(StockCount $stockCount)
    {
        return $this->stockCountService->downloadInitialFile($stockCount);
    }

    public function downloadFinalFile(StockCount $stockCount)
    {
        return $this->stockCountService->downloadFinalFile($stockCount);
    }
}
