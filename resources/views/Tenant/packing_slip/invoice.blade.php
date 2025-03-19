<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="{{url('logo', $general_setting->site_logo)}}" />
    <title>{{$general_setting->site_title}} | Shipping Label</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    @if(!config('database.connections.saleprosaas_landlord'))
        <link rel="icon" type="image/png" href="{{url('logo', $general_setting->site_logo)}}" />
        <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    @else
        <link rel="icon" type="image/png" href="{{url('../../logo', $general_setting->site_logo)}}" />
        <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    @endif

    <style type="text/css">
        * {
            font-size: 14px;
            line-height: 15px;
            font-family: 'Ubuntu', sans-serif;

        }
        .btn {
            padding: 7px 10px;
            text-decoration: none;
            border: none;
            display: block;
            text-align: center;
            margin: 7px;
            cursor:pointer;
        }

        .btn-info {
            background-color: #999;
            color: #FFF;
        }

        .btn-primary {
            background-color: #6449e7;
            color: #FFF;
            width: 100%;
        }



        .centered {
            text-align: center;
            align-content: center;
        }
        small{font-size:11px;}

        @media print {
            * {
                font-size:18px;
                line-height: 16px;
            }
            td,th {padding: 5px 0;}
            .hidden-print {
                display: none !important;
            }
        }
    </style>
  </head>
<body>

<div style="max-width:1000px;margin:0 auto">
    <div class="hidden-print">
        <table>
            <tr>
                <td><a href="{{route('packingSlip.index')}}" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back</a> </td>
                <td><button onclick="window.print();" class="btn btn-primary"><i class="fa fa-print"></i> Print</button></td>
            </tr>
        </table>

    </div>

    <div>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td rowspan="2" class="text-center">
                        <img src="{{url('logo', $general_setting->site_logo)}}" style="margin:10px 0;" height="120" width="120">
                    </td>
                    <td colspan="3">
                        <strong>From:</strong><br>
                        <strong>{{$general_setting->site_title}}</strong><br>
                        {{$sale->warehouse->phone}}<br>
                        {{$sale->warehouse->address}}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <strong>To:</strong><br>
                        @if($sale->is_online)
                        <strong>{{$sale->shipping_name}}</strong><br>
                        {{$sale->shipping_phone}}<br>
                        {{$sale->shipping_address}}, {{$sale->shipping_city}}, {{$lims_sale_data->shipping_country}}<br>
                        @else
                        <strong>{{$sale->customer->name}}</strong><br>
                        {{$sale->customer->phone_number}}<br>
                        {{$sale->customer->address}}, {{$sale->customer->city}}, {{$sale->customer->country}}<br>
                        @endif

                    </td>
                </tr>
                <tr>
                    <td>Invoice No: <strong>{{$sale->reference_no}}</strong></td>
                    <td>Payment Status:
                        @if($sale->payment_status == 1)
                            <strong style="text-transform: uppercase;">{{trans('file.Pending')}}</strong>
                        @elseif($sale->payment_status == 2)
                            <strong style="text-transform: uppercase;">{{trans('file.Due')}}</strong>
                        @elseif($sale->payment_status == 3)
                            <strong style="text-transform: uppercase;">{{trans('file.Partial')}}</strong>
                        @elseif($sale->payment_status == 4)
                            <strong style="text-transform: uppercase;">{{trans('file.Paid')}}</strong>
                        @endif
                    </td>
                    <td colspan="2">COD: <strong>BDT {{$sale->grand_total - $sale->paid_amount}}</strong></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Item</strong></td>
                    <td><strong>Quantity</strong></td>
                    <td><strong>Total</strong></td>
                </tr>
                @foreach($packing_slip_product_data as $key => $packing_slip_product)
                <?php
                    $product = \App\Models\Product::select('name', 'code')->find($packing_slip_product->product_id);
                    if($packing_slip_product->variant_id) {
                        $variant = \App\Models\Variant::select('name')->find($packing_slip_product->variant_id);
                        $product_variant = \App\Models\ProductVariant::select('item_code')->where([
                            ['product_id', $packing_slip_product->product_id],
                            ['variant_id', $packing_slip_product->variant_id]
                        ])->first();
                        $product->name .= ' ['.$variant->name.']';
                        $product->code = $product_variant->item_code;
                    }
                    $sale_product = \App\Models\Product_Sale::select('qty', 'total')
                                    ->where([
                                        ['sale_id', $sale->id],
                                        ['product_id', $packing_slip_product->product_id]
                                    ])->first();
                ?>
                <tr>
                    <td colspan="2">{{$product->name}} [{{$product->code}}]</td>
                    <td>{{$sale_product->qty}}</td>
                    <td>{{$sale_product->total}}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="centered" colspan="3">
                    <?php echo '<img style="margin-top:10px;" src="data:image/png;base64,' . DNS1D::getBarcodePNG($sale->reference_no, 'C128') . '" width="300" alt="barcode"   />'?>
                    </td>
                </tr>
                @if($sale->sale_note)
                <tr>
                    <td colspan="4"><strong>Sale Note: </strong>{{$sale->sale_note}}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function auto_print() {
        window.print()
    }
    //setTimeout(auto_print, 1000);
</script>

</body>
</html>
