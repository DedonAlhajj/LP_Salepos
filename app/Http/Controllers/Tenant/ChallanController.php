<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackingSlip;
use App\Models\Challan;
use App\Models\Product_Sale;
use App\Models\Sale;
use App\Models\Courier;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Account;
use App\Models\Payment;
use App\Models\CashRegister;
use Auth;
use AdnSms\AdnSms;
use DB;

class ChallanController extends Controller
{
    public function index(Request $request)
    {
        if($request->input('status'))
            $status = $request->input('status');
        else
            $status = 0;
        if($request->input('courier_id'))
            $courier_id = $request->input('courier_id');
        else
            $courier_id = 'All Courier';
        $courier_list = Courier::where('is_active', true)->get();
        return view('backend.challan.index', compact('courier_id', 'courier_list', 'status'));
    }

    public function challanData(Request $request)
    {
        $columns = array(
            1 => 'date',
            2 => 'reference_no',
        );

        $courier_id = $request->input('courier_id');
        $status = $request->input('status');

        if($courier_id == 'All Courier' && !$status) {
            $totalData = Challan::count();
        }
        elseif($courier_id == 'All Courier' && $status) {
            $totalData = Challan::where('status', $status)->count();
        }
        elseif($courier_id && $status) {
            $totalData = Challan::where([
                            ['courier_id', $courier_id],
                            ['status', $status]
                        ])->count();
        }
        elseif($courier_id) {
            $totalData = Challan::where([
                            ['courier_id', $courier_id]
                        ])->count();
        }
        elseif($status) {
            $totalData = Challan::where([
                            ['status', $status]
                        ])->count();
        }

        $totalFiltered = $totalData;

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'challans.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))) {
            if($courier_id == 'All Courier' && !$status) {
                $challans = Challan::with('courier')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            }
            elseif($courier_id == 'All Courier' && $status) {
                $challans = Challan::with('courier')
                            ->where('status', $status)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            }
            elseif($courier_id && $status) {
                $challans = Challan::with('courier')
                            ->where([
                                ['courier_id', $courier_id],
                                ['status', $status]
                            ])
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            }
            elseif($courier_id) {
                $challans = Challan::with('courier')
                            ->where([
                                ['courier_id', $courier_id],
                            ])
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            }
            elseif($status) {
                $challans = Challan::with('courier')
                            ->where([
                                ['status', $status],
                            ])
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            }
        }
        else {
            $search = $request->input('search.value');
            if(substr($search, 0, 3) == 'dc-' || substr($search, 0, 3) == 'Dc-' || substr($search, 0, 3) == 'DC-') {
                $search = substr($search, 3, strlen($search));
                $reference_no = $search;
            }
            elseif($search[0] != 'n' && $search[0] != 'N')
                $reference_no = 'N'.$search;
            else
                $reference_no = $search;
            $packing_slip_data = PackingSlip::select('packing_slips.id')
                            ->join('sales', 'packing_slips.sale_id', '=', 'sales.id')
                            ->where('sales.reference_no', $reference_no)
                            ->first();
            if($packing_slip_data) {
                $challans = Challan::where('packing_slip_list', 'LIKE', "%{$packing_slip_data->id}%")->get();
                $totalFiltered = Challan::where('packing_slip_list', 'LIKE', "%{$packing_slip_data->id}%")->count();
            }
            else {
                $challans = Challan::select('challans.*')
                            ->join('couriers', 'challans.courier_id', '=', 'couriers.id')
                            ->with('courier')
                            ->whereDate('challans.created_at', '=' , date('Y-m-d', strtotime($search)))
                            ->orWhere('challans.status', 'LIKE', "%{$search}%")
                            ->orWhere('challans.reference_no', 'LIKE', "%{$search}%")
                            ->orWhere('couriers.name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();

                $totalFiltered = Challan::
                                join('couriers', 'challans.courier_id', '=', 'couriers.id')
                                ->whereDate('challans.created_at', '=' , date('Y-m-d', strtotime($search)))
                                ->orWhere('challans.status', 'LIKE', "%{$search}%")
                                ->orwhere('challans.reference_no', 'LIKE', "%{$search}%")
                                ->orWhere('couriers.name', 'LIKE', "%{$search}%")
                                ->count();
            }
        }

       $data = array();

        if(!empty($challans))
        {
            foreach ($challans as $key => $challan)
            {
                $packingSlipList = explode(",", $challan->packing_slip_list);
                $amountList = explode(",", $challan->amount_list);
                $cashList = explode(",", $challan->cash_list);
                $deliveryChargeList = explode(",", $challan->delivery_charge_list);

                $nestedData['id'] = $challan->id;
                $nestedData['key'] = $key;
                $nestedData['date'] = date(config('date_format').' h:i:s', strtotime($challan->created_at));
                $nestedData['reference'] = 'DC-' . $challan->reference_no;
                $nestedData['sale_reference'] = '';
                foreach($packingSlipList as $index => $packingSlipId) {
                    $packingSlip = PackingSlip::with('sale')->find($packingSlipId);
                    if($packingSlip) {
                        if($index)
                            $nestedData['sale_reference'] .= ', ';
                        $nestedData['sale_reference'] .= $packingSlip->sale->reference_no;
                    }
                }

                $nestedData['courier'] = $challan->courier->name.' ['.$challan->courier->phone_number.']';

                if($challan->status == 'Active')
                    $nestedData['status'] = '<div class="badge badge-success">'.$challan->status.'</div>';
                elseif($challan->status == 'Close')
                    $nestedData['status'] = '<div class="badge badge-danger">'.$challan->status.'</div>';

                // if($challan->deposit_status == 'Deposited')
                //     $nestedData['deposit_status'] = '<div class="badge badge-success">'.$challan->deposit_status.'</div>';
                // elseif($challan->deposit_status == 'Not Deposited')
                //     $nestedData['deposit_status'] = '<div class="badge badge-danger">'.$challan->deposit_status.'</div>';
                // else
                //     $nestedData['deposit_status'] = 'N/A';

                if($challan->closing_date)
                    $nestedData['closing_date'] = date("d/m/Y", strtotime($challan->closing_date));
                else
                    $nestedData['closing_date'] = 'N/A';

                $nestedData['total_amount'] = array_sum($amountList);
                //$nestedData['net_cash'] = array_sum($cashList) - array_sum($deliveryChargeList);

                if($challan->created_by_id)
                    $nestedData['created_by'] = $challan->createdBy->name;
                else
                    $nestedData['created_by'] = 'N/A';

                if($challan->closed_by_id)
                    $nestedData['closed_by'] = $challan->closedBy->name;
                else
                    $nestedData['closed_by'] = 'N/A';

                $nestedData['options'] = '<div class="btn-group">
                                            <a href="'.route('challan.genInvoice', $challan->id).'" class="btn btn-primary" title="Print Challan" target="_blank"><i class="dripicons-print"></i></a>&nbsp';

                if($challan->status == 'Active') {
                    $nestedData['options'] .= '<a href="'.route('challan.finalize', $challan->id).'" class="btn btn-success" title="Finalize Challan"><i class="fa fa-money"></i></a>&nbsp';
                }
                elseif($challan->status == 'Close') {
                    $nestedData['options'] .= '<a href="'.route('challan.moneyReciept', $challan->id).'" class="btn btn-success" title="Print Money Reciept" target="_blank"><i class="fa fa-copy"></i></a>';
                }

                $nestedData['options'] .= '</div>';
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    public function create(Request $request)
    {
        $packing_slip_all = [];
        $packing_slip_id_list = explode(",", $request->packing_slip_id);
        foreach ($packing_slip_id_list as $key => $id) {
            $packing_slip_data = PackingSlip::with('sale')
                                ->where([
                                    ['status', 'Pending'],
                                    ['id', $id]
                                ])->first();
            if($packing_slip_data) {
                $packing_slip_all[] = $packing_slip_data;
            }
        }
        if (!count($packing_slip_all)) {
            return redirect()->back()->with('message', 'Please close previous challan before creating a new one');
        }
        $last_challan = Challan::latest()->first();
        if($last_challan)
            $new_reference = (int)$last_challan->reference_no + 1;
        else
            $new_reference = 1001;
        $courier_list = Courier::where('is_active', true)->get();
        return view('backend.challan.create', compact('new_reference', 'packing_slip_all', 'courier_list'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        //return $data;
        if(isset($data['created_at']))
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        else
            $data['created_at'] = date("Y-m-d H:i:s");
        $last_challan = Challan::orderBy('id', 'desc')->first();
        if($last_challan)
            $data['reference_no'] = (int)$last_challan->reference_no + 1;
        else
            $data['reference_no'] = 1001;
        $data['status'] = 'Active';
        foreach ($data['packing_slip_list'] as $key => $packing_slip_id) {
            $packing_slip = PackingSlip::with('delivery')->select('id', 'status', 'delivery_id')->find($packing_slip_id);

            $packing_slip->delivery->status = 2;
            $packing_slip->delivery->courier_id = $data['courier_id'];
            $packing_slip->delivery->save();

            $packing_slip->status = 'In Transit';
            $packing_slip->save();

        }
        $data['packing_slip_list'] = implode(",", $data['packing_slip_list']);
        $data['amount_list'] = implode(",", $data['amount_list']);
        $data['created_by_id'] = Auth::id();
        Challan::create($data);
        return redirect()->route('challan.index')->with('message', 'Challan created successfully');
    }

    public function genInvoice($id)
    {
        $challan = Challan::with('courier')->find($id);
        return view('backend.challan.invoice', compact('challan'));
    }

    public function finalize($id)
    {
        $challan = Challan::find($id);
        return view('backend.challan.finalize', compact('challan'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        //return $data;
        DB::beginTransaction();
        try {
            $challan = Challan::find($id);
            foreach ($data['cash_list'] as $key => $cash) {
                if(!$cash && !$data['cheque_list'][$key] && !$data['online_payment_list'][$key])
                    $data['status_list'][$key] = 'Failed';
                else
                    $data['status_list'][$key] = 'Delivered';
            }

            $statusList = $data['status_list'];
            $cashList = $data['cash_list'];
            $chequeList = $data['cheque_list'];
            $onlinePaymentList = $data['online_payment_list'];
            //return $cashList;

            $data['cash_list'] = implode(",", $data['cash_list']);
            $data['cheque_list'] = implode(",", $data['cheque_list']);
            $data['online_payment_list'] = implode(",", $data['online_payment_list']);
            $data['delivery_charge_list'] = implode(",", $data['delivery_charge_list']);
            $data['status_list'] = implode(",", $data['status_list']);
            $data['status'] = 'Close';
            $packing_slip_list = explode(",", $challan->packing_slip_list);
            $data['closing_date'] = date("Y-m-d");
            $data['closed_by_id'] = Auth::id();
            //$data['deposit_status'] = 'Not Deposited';
            $challan->update($data);

            foreach ($packing_slip_list as $key => $packing_slip_id) {
                $packing_slip = PackingSlip::with('sale', 'delivery', 'products')->find($packing_slip_id);

                if($statusList[$key] == 'Delivered') {
                    foreach ($packing_slip->products as $product) {
                        $product_sale = Product_Sale::where([
                                            ['sale_id', $packing_slip->sale_id],
                                            ['product_id', $product->id]
                                        ])->first();

                        //update product delivery status
                        $product_sale->is_delivered = true;
                        $product_sale->save();
                    }
                    $packing_slip->status = 'Delivered';
                    $packing_slip->save();
                    //update delivery status
                    $delivery = $packing_slip->delivery;
                    $packing_slip_ids = explode(",", $delivery->packing_slip_ids);
                    $packingSlipStatus = PackingSlip::whereIn("id", $packing_slip_ids)->pluck('status')->toArray();
                    if(!in_array('Pending', $packingSlipStatus)) {
                        $delivery->status = 3;
                        $delivery->save();
                    }
                }

                if($cashList[$key]) {
                    $this->createPayment($cashList[$key], $packing_slip->sale, 'Cash');
                }
                if($onlinePaymentList[$key]) {
                    $this->createPayment($onlinePaymentList[$key], $packing_slip->sale, 'Credit Card');
                }
                if($chequeList[$key]) {
                    $this->createPayment($chequeList[$key], $packing_slip->sale, 'Cheque');
                }
                $delivered_product_number = Product_Sale::where([
                                                    ['sale_id', $packing_slip->sale_id],
                                                    ['is_delivered', true]
                                                ])->count();

                $non_delivered_product_number = Product_Sale::where([
                                                    ['sale_id', $packing_slip->sale_id],
                                                    ['is_delivered', false]
                                                ])->count();
                // if(isset($data['refund_list']) && in_array($packing_slip_id, $data['refund_list'])) {
                //     $packing_slip->status = 'Cancelled';
                //     $packing_slip->sale->status = 'Returned';
                //     $packing_slip->sale->return_note = $data['return_note'][$key];
                //     $packing_slip->sale->return_date = date("Y-m-d");
                //     foreach ($packing_slip->products as $product) {
                //         $product_sale = Product_Sale::where([
                //                             ['sale_id', $packing_slip->sale_id],
                //                             ['product_id', $product->id]
                //                         ])->first();
                //         if($product->type == 'combo') {
                //             $child_ids = explode(",", $product->child_list);
                //             $qty_list = explode(",", $product->qty_list);
                //             foreach ($child_ids as $index => $child_id) {
                //                 $child_product = Product::select('id', 'received_qty')->find($child_id);
                //                 $child_product->received_qty += $qty_list[$index] * $product_sale->qty;
                //                 $child_product->save();
                //             }
                //         }
                //         elseif($product->type == 'standard') {
                //             if($product_sale->package_id) {
                //                 $package_data = ProductSellingPackage::select('rate')->find($product_sale->package_id);
                //                 $product->received_qty += $package_data->rate * $product_sale->qty;
                //             }
                //             else
                //                 $product->received_qty += $product_sale->qty;
                //             $product->save();
                //         }
                //     }
                // }
                if($delivered_product_number && !$non_delivered_product_number)
                    $packing_slip->sale->sale_status = 1;
                // elseif($non_delivered_product_number) {
                //     $packing_slip->status = 'Pending';
                // }
                //checking the payment status
                if($packing_slip->sale->grand_total - $packing_slip->sale->paid_amount == 0)
                    $packing_slip->sale->payment_status = 4;

                $packing_slip->sale->save();
                $packing_slip->save();
            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
        return redirect()->route('challan.index')->with('message', 'Challan finalized successfully');
    }

    public function createPayment($amount, $sale, $paying_method)
    {
        $lims_cash_register_data =  CashRegister::select('id')
                                        ->where([
                                            ['user_id', Auth::id()],
                                            ['warehouse_id', $sale->warehouse_id],
                                            ['status', 1]
                                        ])->first();
        if($lims_cash_register_data)
            $cash_register_id = $lims_cash_register_data->id;
        else
            $cash_register_id = null;
        $account_data = Account::select('id')->where('is_default', 1)->first();
        Payment::create([
            'payment_reference' => 'spr-'.date("Ymd").'-'.date("his"),
            'sale_id' => $sale->id,
            'user_id' => Auth::id(),
            'cash_register_id' => $cash_register_id,
            'account_id' => $account_data->id,
            'amount' => $amount,
            'change' => 0,
            'paying_method' => $paying_method,
        ]);
        $sale->paid_amount += $amount;
        $sale->save();
    }

    public function moneyReciept($id)
    {
        $challan = Challan::find($id);
        return view('backend.challan.money_reciept', compact('challan'));
    }
}
