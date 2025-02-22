<?php

namespace App\Services\Tenant;


use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockCount;
use App\Models\Warehouse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StockCountService
{

    public function authorize($ability)
    {
        if (!Auth::guard('web')->user()->can($ability)) {
            throw new AuthorizationException(__('Sorry! You are not allowed to access this module.'));
        }
    }

    /** index */
    public function index()
    {
        $this->authorize("stock_count");

        $general_setting =DB::table('general_settings')->latest()->first();

        if(!Auth::user()->hasRole(['Admin','Owner']) && $general_setting->staff_access == 'own')
            $stock_count = StockCount::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
        else
            $stock_count = StockCount::orderBy('id', 'desc')->get();

        return [
            'warehouses' => Warehouse::select(['id', 'name'])->get(),
            'brands' => Brand::select(['id', 'title'])->get(),
            'categorys' => Category::select(['id', 'name'])->get(),
            'stock_counts' => $stock_count,

        ];
    }


    /** Store */
    public function storeStockCount($data)
    {
        DB::beginTransaction();

        try {
            // ✅ Retrieve products based on selected filters
            $products = $this->getFilteredProducts($data);

            if ($products->isEmpty()) {
                return ['status' => false, 'message' => 'No product found!'];
            }

            // ✅ Create a temporary CSV file
            $csvFilePath = $this->generateCsvFile($products);

            // ✅ Create StockCount record
            $stockCount = StockCount::create([
                'user_id' => Auth::id(),
                'reference_no' => 'scr-' . now()->format('Ymd-His'),
                'is_adjusted' => false,
                'initial_file' => $csvFilePath,
                'category_id' => isset($data['category_id']) ? implode(",", $data['category_id']) : null,
                'brand_id' => isset($data['brand_id']) ? implode(",", $data['brand_id']) : null,
                'warehouse_id' => $data['warehouse_id'] ?? 1,
            ]);


            DB::commit();

            return ['status' => true, 'message' => 'Stock Count created successfully! Please download the initial file to complete it.'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting edit: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while processing stock count.'];
        }
    }

    private function getFilteredProducts($data)
    {
        return Product::with(['warehouses' => function ($query) use ($data) {
            if (isset($data['warehouse_id'])) { // ✅ تجنب الخطأ إذا لم يكن موجودًا
                $query->where('warehouse_id', $data['warehouse_id']);
            }
        }])
            ->when(isset($data['category_id']), function ($query) use ($data) {
                return $query->whereIn('category_id', $data['category_id']);
            })
            ->when(isset($data['brand_id']), function ($query) use ($data) {
                return $query->whereIn('brand_id', $data['brand_id']);
            })
            ->get();
    }

    private function generateCsvFile($products)
    {
        $directory = public_path('stock_count');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true); // ✅ إنشاء المجلد إذا لم يكن موجودًا
        }

        $filename = $directory . '/' . now()->format('Ymd-His') . '.csv';

        $csvData = [['Product Name', 'Product Code', 'IMEI or Serial Numbers', 'Expected', 'Counted']];

        foreach ($products as $product) {
            foreach ($product->warehouses as $warehouse) {
                $csvData[] = [
                    $product->name,
                    $product->code,
                    str_replace(",", "/", $warehouse->imei_number),
                    $warehouse->qty,
                    ''
                ];
            }
        }

        $file = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        return $filename; // ✅ إرجاع المسار الكامل للملف
    }


    /** FinalLize */
    public function finalizeStockCount(int $stockCountId, UploadedFile $file)
    {
        DB::beginTransaction();

        try {
            // ✅ Make sure the file is in CSV format
            if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
                return ['status' => false, 'message' => 'Please upload a valid CSV file.'];
            }

            // ✅ Retrieve StockCount data
            $stockCount = StockCount::findOrFail($stockCountId);

            // Save File
            $newFilePath = $this->saveCsvFile($stockCount,$file);

            // ✅ Update `final_file` in the database
            $stockCount->update([
                'final_file' => $newFilePath
            ]);

            DB::commit();

            return ['status' => true, 'message' => 'Stock Count finalized successfully!'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error finalizing stock count: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while finalizing stock count.'];
        }
    }

    private function saveCsvFile(StockCount $stockCount , UploadedFile $file)
    {
        // ✅ Specify the new path to save the file
        $directory = public_path('stock_count');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);// ✅ Create the folder if it does not exist
        }

        // ✅ Delete the old file if any
        if (!empty($stockCount->final_file) && file_exists($stockCount->final_file)) {
            unlink($stockCount->final_file);
        }

        // ✅ Save the new file
        $newFilePath = $directory . '/' . now()->format('Ymd-His') . '.csv';
        $file->move($directory, basename($newFilePath));

        return $newFilePath;
    }


    /** Download File **/
    public function downloadInitialFile(StockCount $stockCount): BinaryFileResponse
    {
        $this->authorizeUser($stockCount);

        $filePath = $stockCount->initial_file;

        if (!file_exists($filePath)) {
            abort(404, 'Initial file not found!');
        }

        return response()->download($filePath, 'initial_stock_count.csv', [
            'Content-Type' => 'text/csv',
            'X-Success' => 'true'
        ]);
    }

    public function downloadFinalFile(StockCount $stockCount): BinaryFileResponse
    {
        $this->authorizeUser($stockCount);

        $filePath = $stockCount->final_file;

        if (!file_exists($filePath)) {
            abort(404, 'Final file not found!');
        }

        return response()->download($filePath, 'final_stock_count.csv', [
            'Content-Type' => 'text/csv',
            'X-Success' => 'true'
        ]);
    }

    private function authorizeUser(StockCount $stockCount): void
    {
        if ($stockCount->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }


    /** Stock Dif */
    public function stockDif($id)
    {
        // ✅ Get StockCount data
        $stockCount = StockCount::findOrFail($id);

        // ✅ Path to final file using Storage
        $filePath = $stockCount->final_file;

        // ✅ Check if the file exists
        if (!file_exists($filePath)) {
            Log::error("Stock Count file not found: {$filePath}");
            return [];
        }

        // ✅ Open the file and read the data
        $fileHandle = fopen($filePath, 'r');
        if (!$fileHandle) {
            Log::error("Unable to open the file: {$filePath}");
            return [];
        }

        $i = 0;
        $tempDif = -1000000;
        $data = [];
        $product = [];
        $expected = [];
        $counted = [];
        $difference = [];
        $cost = [];

        // ✅ Read data from file
        while (($currentLine = fgetcsv($fileHandle)) !== false) {
            // ✅ Skip first line
            if ($i > 0 && $this->isDifference($currentLine)) {
                // ✅ Search for the product
                $productData = $this->findProduct($currentLine[1]);

                if ($productData) {
                    $product[] = $this->formatProductName($currentLine[0], $productData);
                    $expected[] = $currentLine[3];
                    $counted[] = $this->getCountedValue($currentLine[4]);
                    $difference[] = $this->calculateDifference($currentLine[3], $currentLine[4], $tempDif);
                    $cost[] = $this->calculateCost($productData->cost, $difference[count($difference) - 1]);
                }
            }
            $i++;
        }

        fclose($fileHandle);

        // ✅ Update StockCount status if differences are not present
        if ($tempDif == -1000000) {
            $stockCount->update(['is_adjusted' => true]);
        }

        // ✅ Preparing data for return
        return $this->prepareData($product, $expected, $counted, $difference, $cost, $stockCount->is_adjusted);
    }

    // ✅ Check if there is a difference between the expected and counted quantity
    private function isDifference($currentLine)
    {
        return $currentLine[3] != $currentLine[4];
    }

    // ✅ Find product based on code
    private function findProduct($productCode)
    {
        $productData = Product::where('code', $productCode)->first();

        if (!$productData) {
            $productData = Product::where('code', 'LIKE', "%{$productCode}%")->first();
        }

        return $productData;
    }

    // ✅ Product name formatting
    private function formatProductName($productName, $productData)
    {
        return "{$productName} [{$productData->code}]";
    }

    // ✅ Get the counted value (handling empty case)
    private function getCountedValue($countedValue)
    {
        return $countedValue ? $countedValue : 0;
    }

    // ✅ Calculate the difference between the expected and counted quantity
    private function calculateDifference($expected, $counted, &$tempDif)
    {
        if ($counted) {
            return $counted - $expected;
        } else {
            $tempDif = $expected * (-1);
            return $tempDif;
        }
    }

    // ✅ Cost calculation
    private function calculateCost($productCost, $difference)
    {
        return $productCost * $difference;
    }

    // ✅ Preparing final data for return
    private function prepareData($product, $expected, $counted, $difference, $cost, $isAdjusted)
    {
        if (count($product)) {
            return [
                $product,
                $expected,
                $counted,
                $difference,
                $cost,
                $isAdjusted
            ];
        }

        return [];
    }



    /** Gty Adjustment */
    public function qtyAdjustment($id)
    {
        try {
            // ✅ Get StockCount data
            $stockCount = StockCount::findOrFail($id);

            // ✅ Get the list of active repositories
            $warehouses = Warehouse::all();

            // ✅ Get the StockCount data associated with the warehouse
            $warehouseId = $stockCount->warehouse_id;

            // ✅ Path to final file using Storage
            $filePath = $stockCount->final_file;

            // ✅ Check if the file exists
            if (!file_exists($filePath)) {
                Log::error("Stock Count file not found: {$filePath}");
                return ['status' => false, 'data' => "Stock Count file not found"];
            }

            // ✅ Read data from file
            $fileHandle = fopen($filePath, 'r');
            if (!$fileHandle) {
                Log::error("Unable to open the file: {$filePath}");
                return ['status' => false, 'data' => "Stock Count file not found"];
            }

            // ✅ Setting up variables to store data
            $i = 0;
            $productIds = [];
            $names = [];
            $codes = [];
            $quantities = [];
            $actions = [];

            // ✅ Read data from CSV file
            while (($currentLine = fgetcsv($fileHandle)) !== false) {
                if ($i > 0 && $this->isDifference($currentLine)) {
                    $product = $this->getProductByCode($currentLine[1]);

                    if ($product) {
                        $productIds[] = $product->id;
                        $names[] = $currentLine[0];
                        $codes[] = $currentLine[1];

                        $tempQty = $this->calculateQuantity($currentLine[3], $currentLine[4]);
                        $quantities[] = abs($tempQty);
                        $actions[] = $tempQty < 0 ? '-' : '+';
                    }
                }
                $i++;
            }

            fclose($fileHandle);

            // ✅ Return data to the View
            return [
                'status' => true,
                'data' => [
                    'warehouses' => $warehouses,
                    'warehouse_id' => $warehouseId,
                    'id' => $id,
                    'productIds' => $productIds,
                    'names' => $names,
                    'codes' => $codes,
                    'quantities' => $quantities,
                    'actions' => $actions,
                ]];
        } catch (\Exception $e) {
            Log::error('Error : ' . $e->getMessage());
            return ['status' => false, 'data' => 'An error occurred while processing qtyAdjustment.'];
        }
    }

    // ✅ Get the product based on the code
    private function getProductByCode($productCode)
    {
        return Product::where('code', $productCode)->first();
    }

    // ✅ Calculate the quantity based on the difference between the expected and counted quantity
    private function calculateQuantity($expectedQty, $countedQty)
    {
        return $countedQty ? $countedQty - $expectedQty : $expectedQty * (-1);
    }

}
