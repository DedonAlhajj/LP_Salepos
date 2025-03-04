<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\TransferData;
use App\Http\Controllers\Controller;
use App\Mail\TransferDetails;
use App\Services\Tenant\ProductService;
use App\Services\Tenant\ProductTransferService;
use App\Services\Tenant\ProductWarehouseService;
use App\Services\Tenant\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Transfer;
use App\Models\ProductTransfer;
use App\Models\ProductVariant;
use App\Models\ProductBatch;
use App\Models\MailSetting;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{

    protected TransferService $transferService;
    protected ProductTransferService $productTransferService;
    protected ProductWarehouseService $productWarehouseService;
    protected ProductService $productService;

    public function __construct(
        TransferService $transferService,
        ProductTransferService $productTransferService,
        ProductWarehouseService $productWarehouseService,
        ProductService $productService)
    {
        $this->transferService = $transferService;
        $this->productTransferService = $productTransferService;
        $this->productWarehouseService = $productWarehouseService;
        $this->productService = $productService;
    }
    public function index(Request $request)
    {
        try {
            $data = $this->transferService->getFilters($request);

            // جلب التحويلات باستخدام خدمة TransferService
            $transfers = $this->transferService->getTransfers($data);

            return view('Tenant.transfer.index', compact('data', 'transfers'));
        } catch (\Exception $e) {
            Log::error("Error in transfer index: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }

    }

    public function productTransferData(int $id): JsonResponse
    {
        try {
            $data = $this->productTransferService->getProductTransferData($id);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error("Error in transfer product TransferData: " . $e->getMessage());
            return response()->json("Error");
        }
    }

    public function create()
    {
        try {
            $data = $this->transferService->getCreateData();
            return view('Tenant.transfer.create', $data);
        } catch (\Exception $e) {
            Log::error("Error in transfer index: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }
    }

    public function getProduct(int $id): JsonResponse
    {
        try {
            return response()->json($this->productWarehouseService->getProductsByWarehouse($id));
        } catch (\Exception $e) {
            Log::error("Error in transfer getProduct: " . $e->getMessage());
            return response()->json("Error");
        }
    }

    public function limsProductSearch(Request $request)
    {
        try {
            return response()->json($this->productService->searchProductTransfer($request->input('data')));
        } catch (\Exception $e) {
            Log::error("Error in transfer limsProductSearch: " . $e->getMessage());
            return response()->json("Error");
        }
    }

    public function store(Request $request)
    {
        try {
            // تعيين البيانات من الـRequest إلى TransferData
            $data = TransferData::fromRequest($request->all());
            $document = $request->file('document');  // إذا كان هناك مستند

            // استدعاء الدالة في TransferService
            $message = $this->transferService->storeTransferData($data, $document);

            return redirect('transfers')->with('message', $message);
        } catch (\Exception $e) {
            Log::error("Error in transfer store: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }
    }


    public function transferByCsv()
    {
        try {
            $data = $this->transferService->getCreateData();
            return view('Tenant.transfer.import', $data);
        } catch (\Exception $e) {
            Log::error("Error in transfer transferByCsv: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }
    }

    public function importTransfer(Request $request)
    {
        //get the file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        $file_handle = fopen($filePath, 'r');
        $i = 0;
        //validate the file
        while (!feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if($current_line && $i > 0){
                $product_data[] = Product::where('code', $current_line[0])->first();
                if(!$product_data[$i-1])
                    return redirect()->back()->with('message', 'Product does not exist!');
                $unit[] = Unit::where('unit_code', $current_line[2])->first();
                if(!$unit[$i-1])
                    return redirect()->back()->with('message', 'Purchase unit does not exist!');
                if(strtolower($current_line[4]) != "no tax"){
                    $tax[] = Tax::where('name', $current_line[4])->first();
                    if(!$tax[$i-1])
                        return redirect()->back()->with('message', 'Tax name does not exist!');
                }
                else
                    $tax[$i-1]['rate'] = 0;

                $qty[] = $current_line[1];
                $cost[] = $current_line[3];
            }
            $i++;
        }

        $data = $request->except('file');
        $data['reference_no'] = 'tr-' . date("Ymd") . '-'. date("his");
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
            $documentName = $data['reference_no'] . '.' . $ext;
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }
        $item = 0;
        $grand_total = $data['shipping_cost'];
        $data['user_id'] = Auth::id();
        Transfer::create($data);
        $lims_transfer_data = Transfer::latest()->first();

        foreach ($product_data as $key => $product) {
            if($product['tax_method'] == 1){
                $net_unit_cost = $cost[$key];
                $product_tax = $net_unit_cost * ($tax[$key]['rate'] / 100) * $qty[$key];
                $total = ($net_unit_cost * $qty[$key]) + $product_tax;
            }
            elseif($product['tax_method'] == 2){
                $net_unit_cost = (100 / (100 + $tax[$key]['rate'])) * $cost[$key];
                $product_tax = ($cost[$key] - $net_unit_cost) * $qty[$key];
                $total = $cost[$key] * $qty[$key];
            }
            if($data['status'] == 1){
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['from_warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product_warehouse->save();
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['to_warehouse_id']]
                ])->first();
                if($product_warehouse) {
                    $product_warehouse->qty += $quantity;
                    $product_warehouse->save();
                }
                else {
                    $product_warehouse = new Product_Warehouse();
                    $product_warehouse->product_id = $product['id'];
                    $product_warehouse->warehouse_id = $data['to_warehouse_id'];
                    $product_warehouse->qty = $quantity;
                    $product_warehouse->save();
                }
            }
            elseif ($data['status'] == 3) {
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['from_warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product_warehouse->save();
            }

            $product_transfer = new ProductTransfer();
            $product_transfer->transfer_id = $lims_transfer_data->id;
            $product_transfer->product_id = $product['id'];
            $product_transfer->qty = $qty[$key];
            $product_transfer->purchase_unit_id = $unit[$key]['id'];
            $product_transfer->net_unit_cost = number_format((float)$net_unit_cost, config('decimal'), '.', '');
            $product_transfer->tax_rate = $tax[$key]['rate'];
            $product_transfer->tax = number_format((float)$product_tax, config('decimal'), '.', '');
            $product_transfer->total = number_format((float)$total, config('decimal'), '.', '');
            $product_transfer->save();
            $lims_transfer_data->total_qty += $qty[$key];
            $lims_transfer_data->total_tax += number_format((float)$product_tax, config('decimal'), '.', '');
            $lims_transfer_data->total_cost += number_format((float)$total, config('decimal'), '.', '');
        }
        $lims_transfer_data->item = $key + 1;
        $lims_transfer_data->grand_total = $lims_transfer_data->total_cost + $lims_transfer_data->shipping_cost;
        $lims_transfer_data->save();
        return redirect('transfers')->with('message', 'Transfer imported successfully');
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('transfers-edit')){
            $lims_warehouse_list = Warehouse::where('is_active',true)->get();
            $lims_transfer_data = Transfer::find($id);
            $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
            return view('backend.transfer.edit', compact('lims_warehouse_list', 'lims_transfer_data', 'lims_product_transfer_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        //return dd($data);
        $document = $request->document;
        $data['created_at'] = date("Y-m-d", strtotime(str_replace("/", "-", $data['created_at'])));

        $lims_transfer_data = Transfer::find($id);

        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $this->fileDelete('documents/transfer/', $lims_transfer_data->document);

            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/transfer', $documentName);
            $data['document'] = $documentName;
        }

        $lims_product_transfer_data = ProductTransfer::where('transfer_id', $id)->get();
        $product_id = $data['product_id'];
        $imei_number = $data['imei_number'];
        $product_batch_id = $data['product_batch_id'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_transfer = [];
        foreach ($lims_product_transfer_data as $key => $product_transfer_data) {
            $old_product_id[] = $product_transfer_data->product_id;
            $old_product_variant_id[] = null;
            $lims_transfer_unit_data = Unit::find($product_transfer_data->purchase_unit_id);
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $product_transfer_data->qty * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $product_transfer_data->qty / $lims_transfer_unit_data->operation_value;
            }

            if($lims_transfer_data->status == 1){
                if($product_transfer_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->to_warehouse_id)->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                }
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id ],
                        ['warehouse_id', $lims_transfer_data->from_warehouse_id ]
                    ])->first();

                    $lims_product_to_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id ],
                        ['warehouse_id', $lims_transfer_data->to_warehouse_id ]
                    ])->first();
                }
                else {
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->to_warehouse_id)->first();
                }

                if($product_transfer_data->imei_number) {
                    //add imei number to from warehouse
                    if($lims_product_from_warehouse_data->imei_number)
                        $lims_product_from_warehouse_data->imei_number .= ',' . $product_transfer_data->imei_number;
                    else
                        $lims_product_from_warehouse_data->imei_number = $product_transfer_data->imei_number;
                    //deduct imei number from to warehouse
                    $imei_numbers = explode(",", $product_transfer_data->imei_number);
                    $all_imei_numbers = explode(",", $lims_product_to_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_to_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_from_warehouse_data->qty += $quantity;
                $lims_product_from_warehouse_data->save();

                $lims_product_to_warehouse_data->qty -= $quantity;
                $lims_product_to_warehouse_data->save();
            }
            elseif($lims_transfer_data->status == 3) {
                if($product_transfer_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_transfer_data->product_id, $product_transfer_data->variant_id)->first();
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($product_transfer_data->product_id, $product_transfer_data->variant_id, $lims_transfer_data->from_warehouse_id)->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                }
                elseif($product_transfer_data->product_batch_id) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_transfer_data->product_batch_id ],
                        ['warehouse_id', $lims_transfer_data->from_warehouse_id ]
                    ])->first();
                }
                else {
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_transfer_data->product_id, $lims_transfer_data->from_warehouse_id)->first();
                }
                if($product_transfer_data->imei_number) {
                    //add imei number to from warehouse
                    if($lims_product_from_warehouse_data->imei_number)
                        $lims_product_from_warehouse_data->imei_number .= ',' . $product_transfer_data->imei_number;
                    else
                        $lims_product_from_warehouse_data->imei_number = $product_transfer_data->imei_number;
                }
                $lims_product_from_warehouse_data->qty += $quantity;
                $lims_product_from_warehouse_data->save();
            }

            if($product_transfer_data->variant_id && !(in_array($old_product_variant_id[$key], $product_variant_id)) ){
                $product_transfer_data->delete();
            }
            elseif( !(in_array($old_product_id[$key], $product_id)) ){
                $product_transfer_data->delete();
            }
        }

        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::select('is_variant')->find($pro_id);
            $lims_transfer_unit_data = Unit::where('unit_name', $purchase_unit[$key])->first();
            $variant_id = null;
            $product_transfer['product_batch_id'] = null;
            //unit conversion
            if ($lims_transfer_unit_data->operator == '*') {
                $quantity = $qty[$key] * $lims_transfer_unit_data->operation_value;
            } else {
                $quantity = $qty[$key] / $lims_transfer_unit_data->operation_value;
            }

            if($data['status'] == 1) {
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('variant_id')->find($product_variant_id[$key]);
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['to_warehouse_id'])->first();
                    $variant_id = $lims_product_variant_data->variant_id;
                }
                elseif($product_batch_id[$key]) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_batch_id[$key] ],
                        ['warehouse_id', $data['from_warehouse_id'] ]
                    ])->first();

                    $lims_product_to_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_batch_id[$key] ],
                        ['warehouse_id', $data['to_warehouse_id'] ]
                    ])->first();
                    $product_transfer['product_batch_id'] = $product_batch_id[$key];
                }
                else{
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['from_warehouse_id'])->first();
                    $lims_product_to_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['to_warehouse_id'])->first();
                }
                //deduct imei number if available
                if($imei_number[$key]) {
                    $imei_numbers = explode(",", $imei_number[$key]);
                    $all_imei_numbers = explode(",", $lims_product_from_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_from_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_from_warehouse_data->qty -= $quantity;
                $lims_product_from_warehouse_data->save();

                if($lims_product_to_warehouse_data){
                    $lims_product_to_warehouse_data->qty += $quantity;
                }
                else{
                    $lims_product_to_warehouse_data = new Product_Warehouse();
                    $lims_product_to_warehouse_data->product_id = $pro_id;
                    $lims_product_to_warehouse_data->variant_id = $variant_id;
                    $lims_product_to_warehouse_data->product_batch_id = $product_transfer['product_batch_id'];
                    $lims_product_to_warehouse_data->warehouse_id = $data['to_warehouse_id'];
                    $lims_product_to_warehouse_data->qty = $quantity;
                }
                //add imei number if available
                if($imei_number[$key]) {
                    if($lims_product_to_warehouse_data->imei_number)
                        $lims_product_to_warehouse_data->imei_number .= ',' . $imei_number[$key];
                    else
                        $lims_product_to_warehouse_data->imei_number = $imei_number[$key];
                }
                $lims_product_to_warehouse_data->save();
            }
            elseif($data['status'] == 3) {
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('variant_id')->find($product_variant_id[$key]);
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['from_warehouse_id'])->first();
                    $variant_id = $lims_product_variant_data->variant_id;
                }
                elseif($product_batch_id[$key]) {
                    $lims_product_from_warehouse_data = Product_Warehouse::where([
                        ['product_batch_id', $product_batch_id[$key] ],
                        ['warehouse_id', $data['from_warehouse_id'] ]
                    ])->first();
                    $product_transfer['product_batch_id'] = $product_batch_id[$key];
                }
                else{
                    $lims_product_from_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['from_warehouse_id'])->first();
                }
                //deduct imei number if available
                if($imei_number[$key]) {
                    $imei_numbers = explode(",", $imei_number[$key]);
                    $all_imei_numbers = explode(",", $lims_product_from_warehouse_data->imei_number);
                    foreach ($imei_numbers as $number) {
                        if (($j = array_search($number, $all_imei_numbers)) !== false) {
                            unset($all_imei_numbers[$j]);
                        }
                    }
                    $lims_product_from_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                }

                $lims_product_from_warehouse_data->qty -= $quantity;
                $lims_product_from_warehouse_data->save();
            }

            $product_transfer['product_id'] = $pro_id;
            $product_transfer['variant_id'] = $variant_id;
            $product_transfer['imei_number'] = $imei_number[$key];
            $product_transfer['transfer_id'] = $id;
            $product_transfer['qty'] = $qty[$key];
            $product_transfer['purchase_unit_id'] = $lims_transfer_unit_data->id;
            $product_transfer['net_unit_cost'] = $net_unit_cost[$key];
            $product_transfer['tax_rate'] = $tax_rate[$key];
            $product_transfer['tax'] = $tax[$key];
            $product_transfer['total'] = $total[$key];

            if($lims_product_data->is_variant && in_array($product_variant_id[$key], $old_product_variant_id) ) {
                ProductTransfer::where([
                    ['transfer_id', $id],
                    ['product_id', $pro_id],
                    ['variant_id', $variant_id]
                ])->update($product_transfer);
            }
            elseif($variant_id == null && in_array($pro_id, $old_product_id) ){
                ProductTransfer::where([
                    ['transfer_id', $id],
                    ['product_id', $pro_id]
                ])->update($product_transfer);
            }
            else
                ProductTransfer::create($product_transfer);
        }

        $lims_transfer_data->update($data);
        return redirect('transfers')->with('message', 'Transfer updated successfully');
    }


    public function deleteBySelection(Request $request)
    {
        $transferIds = $request->input('transferIdArray');

        try {
            $this->transferService->deleteBySelection($transferIds);
            return response()->json('Transfers deleted successfully!', 200);
        } catch (\Exception $e) {
            Log::error("Error deleting transfers deleteBySelection: " . $e->getMessage());
            return response()->json('Failed to delete transfers.', 500);
        }
    }


    public function destroy($id)
    {
        try {
            $this->transferService->deleteTransfer($id);
            return redirect('transfers')->with('message', 'Transfer deleted successfully');
        } catch (\Exception $e) {
            Log::error("Error deleting destroy: " . $e->getMessage());
            return redirect('transfers')->with('not_permitted', 'Transfer deleted successfully');
        }

    }
}
