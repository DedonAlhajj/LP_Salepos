<?php

namespace App\Services\Tenant;

use App\DTOs\ReturnPurchaseDTO;
use App\DTOs\SaleReturnDTO;
use App\Models\Purchase;
use App\Models\ReturnPurchase;
use App\Models\Returns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductHistoryService
{
    public function getSaleHistory($product_id, $warehouse_id, $starting_date, $ending_date)
    {

        $cacheKey = "sales_history_{$product_id}_{$warehouse_id}_{$starting_date}_{$ending_date}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($product_id, $warehouse_id, $starting_date, $ending_date) {
            try {
                $user = Auth::guard('web')->user();
                $query = Sale::with(['customer', 'warehouse', 'products.unit'])
                    ->forProduct($product_id)
                    ->forWarehouse($warehouse_id)
                    ->forDateRange($starting_date, $ending_date)
                    ->forUserAccess();

                return $query->get()->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'reference_no' => $sale->reference_no,
                        'created_at' => $sale->created_at->format('Y-m-d'),
                        'customer_name' => $sale->customer->name ?? 'N/A',
                        'customer_number' => $sale->customer->phone_number ?? 'N/A',
                        'warehouse_name' => $sale->warehouse->name ?? 'N/A',
                        'products' => $sale->products->map(function ($product) {
                            $unit_price = $product->qty ? $product->total / $product->qty : 0;
                            return [
                                'qty' => $product->qty,
                                'unit' => $product->unit ? $product->unit->short_name : 'N/A',
                                'unit_price' => $unit_price,
                                'total' => $product->total
                            ];
                        })
                    ];
                });

            } catch (Exception $e) {
                Log::error("خطأ في جلب بيانات المبيعات: " . $e->getMessage());
                throw new Exception("operation sale failed: " . $e->getMessage());
            }
        });
    }


    public function getPurchaseHistory($product_id, $warehouse_id, $starting_date, $ending_date)
    {
        $cacheKey = "purchases_history_{$product_id}_{$warehouse_id}_{$starting_date}_{$ending_date}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($product_id, $warehouse_id, $starting_date, $ending_date) {
            try {
                $query = Purchase::with(['supplier', 'warehouse', 'products.unit'])
                    ->forProduct($product_id)
                    ->forWarehouse($warehouse_id)
                    ->forDateRange($starting_date, $ending_date)
                    ->forUserAccess();

                return $query->get()->map(function ($purchase) {
                    return [
                        'id' => $purchase->id,
                        'reference_no' => $purchase->reference_no,
                        'created_at' => $purchase->created_at->format('Y-m-d'),
                        'supplier_name' => $purchase->supplier->name ?? 'N/A',
                        'supplier_number' => $purchase->supplier->phone_number ?? 'N/A',
                        'warehouse_name' => $purchase->warehouse->name ?? 'N/A',
                        'products' => $purchase->products->map(function ($product) {
                            $unit_price = $product->qty ? $product->total / $product->qty : 0;
                            return [
                                'qty' => $product->qty,
                                'unit' => $product->unit ? $product->unit->short_name : 'N/A',
                                'unit_price' => $unit_price,
                                'total' => $product->total
                            ];
                        })
                    ];
                });
            } catch (Exception $e) {
                Log::error("خطأ في جلب بيانات المشتريات: " . $e->getMessage());
                throw new Exception("operation purchase failed: " . $e->getMessage());
            }
        });
    }


    public function getSaleReturnHistoryData(Request $request)
    {
        try {
            $columns = [1 => 'created_at', 2 => 'reference_no'];
            $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
            $orderDir = $request->input('order.0.dir', 'desc');

            $product_id = $request->input('product_id');
            $warehouse_id = $request->input('warehouse_id');
            $starting_date = $request->input('starting_date');
            $ending_date = $request->input('ending_date');
            $search = $request->input('search.value');
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);

            // تحسين تحميل البيانات عبر join للحصول على unit_code مباشرة
            $query = Returns::query()
                ->select([
                    'returns.id',
                    'returns.created_at',
                    'returns.reference_no',
                    'returns.warehouse_id',
                    'returns.customer_id',
                    'product_returns.total',
                    'product_returns.qty',
                    'units.unit_code',
                ])
                ->join('product_returns', 'returns.id', '=', 'product_returns.return_id')
                ->leftJoin('units', 'units.id', '=', 'product_returns.sale_unit_id') // تعديل هنا
                ->with([
                    'customer:id,name',
                    'warehouse:id,name',
                ])
                ->filterByProduct($product_id)
                ->filterByWarehouse($warehouse_id)
                ->filterByDateRange($starting_date, $ending_date)
                ->forUserAccess()
                ->search($search);


            $totalData = (clone $query)->count(); // حساب العدد مرة واحدة

            $returnSales = $query->orderBy($orderColumn, $orderDir)
                ->offset($start)
                ->limit($limit)
                ->get();

            // تحويل البيانات إلى DTO
            $data = $returnSales->map(fn($returnSale) => SaleReturnDTO::fromModel($returnSale));

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => count($data),
                "data" => $data
            ]);

        } catch (Exception $e) {
            Log::error("Error fetching Sale Return History: " . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء جلب البيانات'], 500);
        }
    }


    public function getPurchaseReturnHistoryData(Request $request)
    {
        try {
            $orderColumn = match ($request->input('order.0.column')) {
                1 => 'created_at',
                2 => 'reference_no',
                default => 'created_at'
            };
            $orderDir = $request->input('order.0.dir', 'desc');

            $query = ReturnPurchase::query()
                ->with(['supplier:id,name,phone_number', 'warehouse:id,name', 'productReturns'])
                ->filterByProduct($request->input('product_id'))
                ->filterByWarehouse($request->input('warehouse_id'))
                ->filterByDateRange($request->input('starting_date'), $request->input('ending_date'))
                ->forUserAccess()
                ->search($request->input('search.value'));

            $totalData = (clone $query)->count();

            $returnPurchases = $query->orderBy($orderColumn, $orderDir)
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 10))
                ->get();

            $data = $returnPurchases->map(fn($purchase) => ReturnPurchaseDTO::fromModel($purchase));

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => count($data),
                "data" => $data
            ]);

        } catch (Exception $e) {
            Log::error("Error fetching Purchase Return History: " . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء جلب البيانات'], 500);
        }
    }




}
