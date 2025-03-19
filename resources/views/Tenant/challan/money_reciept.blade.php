<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="{{url('images/logo_2.png')}}" />
    <title>{{$general_setting->site_title}} | Money Receipt</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">

    @if(!config('database.connections.saleprosaas_landlord'))
        <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    @else
    <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    @endif

    <style type="text/css">
        * {
            font-size: 14px;
            line-height: 24px;
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


        table {width: 50% !important;}


        .centered {
            text-align: center;
            align-content: center;
        }
        small{font-size:11px;}

        @media print {
            * {
                font-size:20px;
                line-height: 20px;
            }
            td,th {padding: 5px 0;}
            .hidden-print {
                display: none !important;
            }
            @page { margin: 0; } body { margin: 0.5cm; margin-bottom:1.6cm; }
            #rider-copy { page-break-after: always; }
        }
    </style>
  </head>
<body>

<div style="max-width:800px;margin:0 auto">
    <div class="hidden-print">
        <table>
            <tr>
                <td><a href="{{route('challan.index')}}" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back</a> </td>
                <td><button onclick="window.print();" class="btn btn-primary"><i class="fa fa-print"></i> Print</button></td>
            </tr>
        </table>

    </div>

    <div id="office-copy">
        <br><br>
        <h1 class="text-center">MONEY RECIEPT</h1>
        <h2 class="text-center">Office Copy</h2><br>
        <p>Reference: DC-{{$challan->reference_no}}</p>
        <p>Date: {{date($general_setting->date_format, strtotime($challan->created_at->toDateString()))}}</p>
        <p>Courier: {{$challan->courier->name.' ['.$challan->courier->phone_number.']'}}</p>
        <?php
            $packing_slip_list = explode(",", $challan->packing_slip_list);
            $cash_list = explode(",", $challan->cash_list);
            $bkash_list = explode(",", $challan->bkash_list);
            $online_payment_list = explode(",", $challan->online_payment_list);
            $delivery_charge_list = explode(",", $challan->delivery_charge_list);
            $status_list = explode(",", $challan->status_list);
            $cash_sum = 0;
            $bkash_sum = 0;
            $online_payment_sum = 0;
            $delivery_charge_sum = 0;
        ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>PS Reference</th>
                    <th>Order Reference</th>
                    <th>Cash</th>
                    <th>Cheque</th>
                    <th>Online Payment</th>
                    <th>Delivery Charge</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($packing_slip_list as $key=>$packing_slip_id)
            <?php
                $packing_slip = \App\Models\PackingSlip::with('sale')->find($packing_slip_id);
            	if($cash_list[$key])
            		$cash_sum += $cash_list[$key];
            	elseif($bkash_list[$key])
            		$bkash_sum += $bkash_list[$key];
            	elseif($online_payment_list[$key])
            		$online_payment_sum += $online_payment_list[$key];

                if($challan->delivery_charge_list && $delivery_charge_list[$key])
                    $delivery_charge_sum += $delivery_charge_list[$key];
                else
                    $delivery_charge_list[$key] = '';
            ?>
            <tr>
                <td>{{$key+1}}</td>
                <td>P{{$packing_slip->reference_no}}</td>
                <td>{{$packing_slip->sale->reference_no}}</td>
                <td>{{$cash_list[$key]}}</td>
                <td>{{$bkash_list[$key]}}</td>
                <td>{{$online_payment_list[$key]}}</td>
                <td>{{$delivery_charge_list[$key]}}</td>
                <td>{{$status_list[$key]}}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th>{{$cash_sum}}</th>
                    <th>{{$bkash_sum}}</th>
                    <th>{{$online_payment_sum}}</th>
                    <th>{{$delivery_charge_sum}}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <p><strong>Total Cash: </strong>{{$cash_sum}}</p>
        <p><strong>Total Delivery Charge: </strong>{{$delivery_charge_sum}}</p>
        <p><strong>Net Cash: </strong>{{$cash_sum - $delivery_charge_sum}}</p>
        <br><br><br>
        <div class="row">
            <div class="col-md-6">
                <hr style="border-top: 2px solid black">
                <p>Rider Signature</p>
            </div>
            <div class="col-md-6">
                <hr style="border-top: 2px solid black">
                <p>Authorized Signature</p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function auto_print() {
        window.print()
    }
    setTimeout(auto_print, 1000);
</script>

</body>
</html>
