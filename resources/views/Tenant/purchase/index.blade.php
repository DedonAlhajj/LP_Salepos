@extends('Tenant.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Purchase List')}}</h3>
            </div>
            {!! Form::open(['route' => 'purchases.index', 'method' => 'get']) !!}
            <div class="row ml-1 mt-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <label><strong>{{trans('file.Date')}}</strong></label>
                        <input type="text" class="daterangepicker-field form-control" value="{{$filters['starting_date']}} To {{$filters['ending_date']}}" required />
                        <input type="hidden" name="starting_date" value="{{$filters['starting_date']}}" />
                        <input type="hidden" name="ending_date" value="{{$filters['ending_date']}}" />
                    </div>
                </div>
                <div class="col-md-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="form-group">
                        <label><strong>{{trans('file.Warehouse')}}</strong></label>
                        <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                            <option value="0">{{trans('file.All Warehouse')}}</option>
                            @foreach($lims_warehouse_list as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label><strong>{{trans('file.Purchase Status')}}</strong></label>
                        <select id="purchase-status" class="form-control" name="purchase_status">
                            <option value="0">{{trans('file.All')}}</option>
                            <option value="1">{{trans('file.Recieved')}}</option>
                            <option value="2">{{trans('file.Partial')}}</option>
                            <option value="3">{{trans('file.Pending')}}</option>
                            <option value="4">{{trans('file.Ordered')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label><strong>{{trans('file.Payment Status')}}</strong></label>
                        <select id="payment-status" class="form-control" name="payment_status">
                            <option value="0">{{trans('file.All')}}</option>
                            <option value="1">{{trans('file.Due')}}</option>
                            <option value="2">{{trans('file.Paid')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" id="filter-btn" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @can('purchases-add')
            <a href="{{route('purchases.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Purchase')}}</a>&nbsp;
            <a href="{{url('purchases/purchase_by_csv')}}" class="btn btn-primary"><i class="dripicons-copy"></i> {{trans('file.Import Purchase')}}</a>
            @endcan
    </div>
    <div class="table-responsive">
        <table id="purchase-table" class="table purchase-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Supplier')}}</th>
                    <th>{{trans('file.Purchase Status')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th>{{trans('file.Returned Amount')}}</th>
                    <th>{{trans('file.Paid')}}</th>
                    <th>{{trans('file.Due')}}</th>
                    <th>{{trans('file.Payment Status')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($purchases as $key => $purchase)
                <tr data-purchase='@json($purchase)'>
                    <td>{{ $key}}</td>
                    <td>{{ $purchase['date'] }}</td>
                    <td>{{ $purchase['reference_no'] }}</td>
                    <td>{{ $purchase['supplier']['name'] }}</td>
                    <td>
                        @if($purchase['purchase_status'] == trans('file.Recieved') || $purchase['purchase_status'] == trans('file.Partial'))
                            <div class="badge badge-success">{{$purchase['purchase_status']}}</div>
                        @else
                            <div class="badge badge-danger">{{$purchase['purchase_status']}}</div>
                        @endif
                    </td>
                    <td>{{ $purchase['grand_total'] }}</td>
                    <td>{{ $purchase['returned_amount'] }}</td>
                    <td>{{ $purchase['paid_amount'] }}</td>
                    <td>{{ $purchase['due'] }}</td>
                    <td>
                        @if($purchase['payment_status'] == trans('file.Paid'))
                            <div class="badge badge-success">{{$purchase['payment_status']}}</div>
                        @else
                            <div class="badge badge-danger">{{$purchase['payment_status']}}</div>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans("file.action") }}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default">
                                <li>
                                    <button type="button" class="btn btn-link view">
                                        <i class="fa fa-eye"></i> {{ trans('file.View') }}
                                    </button>
                                </li>

                                <li>
                                    <a href="{{ route('purchase.duplicate', $purchase['id']) }}" class="btn btn-link">
                                        <i class="fa fa-copy"></i> {{ trans('file.Duplicate') }}
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('purchases.edit', $purchase['id']) }}" class="btn btn-link">
                                        <i class="dripicons-document-edit"></i> {{ trans('file.edit') }}
                                    </a>
                                </li>

                                <li>
                                    <button type="button" class="get-payment btn btn-link" data-id="{{ $purchase['id'] }}">
                                        <i class="fa fa-money"></i> {{ trans('file.View Payment') }}
                                    </button>
                                </li>

                                <li>
                                    <button type="button" class="add-payment btn btn-link" data-id="{{ $purchase['id'] }}" data-toggle="modal" data-target="#add-payment">
                                        <i class="fa fa-plus"></i> {{ trans('file.Add Payment') }}
                                    </button>
                                </li>

                                <li>
                                    <form action="{{ route('purchases.destroy', $purchase['id']) }}" method="POST" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link">
                                            <i class="dripicons-trash"></i> {{ trans('file.delete') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="purchase-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                <div class="col-md-6 d-print-none">
                    <button id="print-btn" type="button" class="btn btn-default btn-sm"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                </div>
                <div class="col-md-6 d-print-none">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="col-md-12">
                    <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                </div>
                <div class="col-md-12 text-center">
                    <i style="font-size: 15px;">{{trans('file.Purchase Details')}}</i>
                </div>
            </div>
        </div>
            <div id="purchase-content" class="modal-body"></div>
            <br>
            <table class="table table-bordered product-purchase-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>{{trans('file.Batch No')}}</th>
                    <th>Qty</th>
                    <th>{{trans('file.Unit Cost')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Discount')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="purchase-footer" class="modal-body"></div>
      </div>
    </div>
</div>

<div id="view-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.All Payment')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover payment-list">
                    <thead>
                        <tr>
                            <th>{{trans('file.date')}}</th>
                            <th>{{trans('file.Reference No')}}</th>
                            <th>{{trans('file.Account')}}</th>
                            <th>{{trans('file.Amount')}}</th>
                            <th>{{trans('file.Paid By')}}</th>
                            <th>{{trans('file.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="add-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Payment')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'purchase.add-payment', 'method' => 'post', 'class' => 'payment-form' ]) !!}
                    <div class="row">
                        <input type="hidden" name="balance">
                        <div class="col-md-6">
                            <label>{{trans('file.Recieved Amount')}} *</label>
                            <input type="text" name="paying_amount" class="form-control numkey"  step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label>{{trans('file.Paying Amount')}} *</label>
                            <input type="text" id="amount" name="amount" class="form-control"  step="any" required>
                        </div>
                        <div class="col-md-6 mt-1">
                            <label>{{trans('file.Change')}} : </label>
                            <p class="change ml-2">{{number_format(0, $general_setting->decimal, '.', '')}}</p>
                        </div>
                        <div class="col-md-6 mt-1">
                            <label>{{trans('file.Paid By')}}</label>
                            <select name="paid_by_id" class="form-control">
                                <option value="1">Cash</option>
                                <option value="3">Credit Card</option>
                                <option value="4">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <div class="card-element" class="form-control">
                        </div>
                        <div class="card-errors" role="alert"></div>
                    </div>
                    <div id="cheque">
                        <div class="form-group">
                            <label>{{trans('file.Cheque Number')}} *</label>
                            <input type="text" name="cheque_no" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label> {{trans('file.Account')}}</label>
                        <select class="form-control selectpicker" name="account_id">
                        @foreach($lims_account_list as $account)
                            @if($account->is_default)
                            <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @else
                            <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('file.Payment Note')}}</label>
                        <textarea rows="3" class="form-control" name="payment_note"></textarea>
                    </div>

                    <input type="hidden" name="purchase_id">

                    <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<div id="edit-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Payment')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'purchase.update-payment', 'method' => 'post', 'class' => 'payment-form' ]) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <label>{{trans('file.Recieved Amount')}} *</label>
                            <input type="text" name="edit_paying_amount" class="form-control numkey"  step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label>{{trans('file.Paying Amount')}} *</label>
                            <input type="text" name="edit_amount" class="form-control"  step="any" required>
                        </div>
                        <div class="col-md-6 mt-1">
                            <label>{{trans('file.Change')}} : </label>
                            <p class="change ml-2">{{number_format(0, $general_setting->decimal, '.', '')}}</p>
                        </div>
                        <div class="col-md-6 mt-1">
                            <label>{{trans('file.Paid By')}}</label>
                            <select name="edit_paid_by_id" class="form-control selectpicker">
                                <option value="1">Cash</option>
                                <option value="3">Credit Card</option>
                                <option value="4">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <div class="card-element" class="form-control">
                        </div>
                        <div class="card-errors" role="alert"></div>
                    </div>
                    <div id="edit-cheque">
                        <div class="form-group">
                            <label>{{trans('file.Cheque Number')}} *</label>
                            <input type="text" name="edit_cheque_no" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label> {{trans('file.Account')}}</label>
                        <select class="form-control selectpicker" name="account_id">
                        @foreach($lims_account_list as $account)
                            <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{trans('file.Payment Note')}}</label>
                        <textarea rows="3" class="form-control" name="edit_payment_note"></textarea>
                    </div>

                    <input type="hidden" name="payment_id">

                    <button type="submit" class="btn btn-primary">{{trans('file.update')}}</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $(".daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period){
        var starting_date = startDate.format('YYYY-MM-DD');
        var ending_date = endDate.format('YYYY-MM-DD');
        var title = starting_date + ' To ' + ending_date;
        $(this).val(title);
        $('input[name="starting_date"]').val(starting_date);
        $('input[name="ending_date"]').val(ending_date);
      }
    });

    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-list-menu").addClass("active");

    @if($lims_pos_setting_data)
        var public_key = <?php echo json_encode($lims_pos_setting_data->stripe_public_key) ?>;
    @endif


    var purchase_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    var starting_date = <?php echo json_encode($filters['starting_date']); ?>;
    var ending_date = <?php echo json_encode($filters['ending_date']); ?>;
    var warehouse_id = <?php echo json_encode($filters['warehouse_id']); ?>;
    var purchase_status = <?php echo json_encode($filters['purchase_status']); ?>;
    var payment_status = <?php echo json_encode($filters['payment_status']); ?>;

    var columns = [
        {"data": "key"},
        {"data": "date"},
        {"data": "reference_no"},
        {"data": "supplier"},
        {"data": "purchase_status"},
        {"data": "grand_total"},
        {"data": "returned_amount"},
        {"data": "paid_amount"},
        {"data": "due"},
        {"data": "payment_status"}
    ];

    columns.push({"data": "options"});

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#warehouse_id").val(warehouse_id);
    $("#purchase-status").val(purchase_status);
    $("#payment-status").val(payment_status);

    $('.selectpicker').selectpicker('refresh');

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    function confirmDeletePayment() {
        if (confirm("Are you sure want to delete? If you delete this money will be refunded")) {
            return true;
        }
        return false;
    }

    $(document).on("click", "tr.purchase-link td:not(:first-child, :last-child)", function(){
        var purchase = $(this).closest('tr').data('purchase');
        purchaseDetails(purchase);
    });

    $(document).on("click", ".view", function(){
        // احصل على البيانات المخزنة في data-purchase
        var purchase = $(this).closest('tr').data('purchase');  // جلب البيانات من الـ data-attribute

        // تمرير البيانات إلى الدالة purchaseDetails
        purchaseDetails(purchase);
    });

    $("#print-btn").on("click", function(){
        var divContents = document.getElementById("purchase-details").innerHTML;
        var a = window.open('');
        a.document.write('<html>');
        a.document.write('<body><style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left;}td{padding:10px}table, th, td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        setTimeout(function(){a.close();},10);
        a.print();
    });

    $(document).on("click", "table.purchase-list tbody .add-payment", function(event) {
        $("#cheque").hide();
        $(".card-element").hide();
        $('select[name="paid_by_id"]').val(1);
        rowindex = $(this).closest('tr').index();
        var purchase_id = $(this).data('id').toString();
        var balance = $('table.purchase-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(9)').text();
        balance = parseFloat(balance.replace(/,/g, ''));
        $('input[name="amount"]').val(balance);
        $('input[name="balance"]').val(balance);
        $('input[name="paying_amount"]').val(balance);
        $('input[name="purchase_id"]').val(purchase_id);
    });

    $(document).on("click", "table.purchase-list tbody .get-payment", function(event) {
        var id = $(this).data('id').toString();

        $.get('purchases/getpayment/' + id, function(data) {
            $(".payment-list tbody").remove(); // حذف القائمة القديمة
            var newBody = $("<tbody>");

            // استخدم `forEach` بدلاً من الفرض اليدوي للقيم
            data.forEach(function(payment) {
                var newRow = $("<tr>");
                var cols = '';

                cols += '<td>' + payment.date + '</td>';
                cols += '<td>' + payment.payment_reference + '</td>';
                cols += '<td>' + payment.account_name + '</td>';
                cols += '<td>' + payment.paid_amount + '</td>';
                cols += '<td>' + payment.paying_method + '</td>';
                cols += '<td><div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                cols += '<li><button type="button" class="btn btn-link edit-btn" data-id="' + payment.payment_id +'" data-clicked=false data-toggle="modal" data-target="#edit-payment"><i class="dripicons-document-edit"></i> Edit</button></li><li class="divider"></li>';
                cols += '{{ Form::open(['route' => 'purchase.delete-payment', 'method' => 'post'] ) }}<li><input type="hidden" name="id" value="' + payment.payment_id + '" /> <button type="submit" class="btn btn-link" onclick="return confirmDeletePayment()"><i class="dripicons-trash"></i> Delete</button></li>{{ Form::close() }}';
                cols += '</ul></div></td>';


                newRow.append(cols);
                newBody.append(newRow);
            });

            $("table.payment-list").append(newBody);
            $('#view-payment').modal('show'); // عرض المودال
        });
    });


    $(document).on("click", "table.payment-list .edit-btn", function(event) {
        $(".edit-btn").attr('data-clicked', true);
        $(".card-element").hide();
        $("#edit-cheque").hide();
        $('#edit-payment select[name="edit_paid_by_id"]').prop('disabled', false);
        var id = $(this).data('id').toString();
        $.each(payment_id, function(index){
            if(payment_id[index] == parseFloat(id)){
                $('input[name="payment_id"]').val(payment_id[index]);
                $('#edit-payment select[name="account_id"]').val(account_id[index]);
                if(paying_method[index] == 'Cash')
                    $('select[name="edit_paid_by_id"]').val(1);
                else if(paying_method[index] == 'Credit Card'){
                    $('select[name="edit_paid_by_id"]').val(3);
                    @if($lims_pos_setting_data && (strlen($lims_pos_setting_data->stripe_public_key)>0) && (strlen($lims_pos_setting_data->stripe_secret_key )>0))
                        $.getScript( "public/vendor/stripe/checkout.js" );
                        $(".card-element").show();
                    @endif
                    $("#edit-cheque").hide();
                    $('#edit-payment select[name="edit_paid_by_id"]').prop('disabled', true);
                }
                else{
                    $('select[name="edit_paid_by_id"]').val(4);
                    $("#edit-cheque").show();
                    $('input[name="edit_cheque_no"]').val(cheque_no[index]);
                    $('input[name="edit_cheque_no"]').attr('required', true);
                }
                $('input[name="edit_date"]').val(payment_date[index]);
                $("#payment_reference").html(payment_reference[index]);
                $('input[name="edit_amount"]').val(paid_amount[index]);
                $('input[name="edit_paying_amount"]').val(paying_amount[index]);
                $('.change').text(change[index]);
                $('textarea[name="edit_payment_note"]').val(payment_note[index]);
                return false;
            }
        });
        $('.selectpicker').selectpicker('refresh');
        $('#view-payment').modal('hide');
    });

    $('select[name="paid_by_id"]').on("change", function() {
        var id = $('select[name="paid_by_id"]').val();
        $('input[name="cheque_no"]').attr('required', false);
        $(".payment-form").off("submit");
        if (id == 3) {
            $.getScript( "public/vendor/stripe/checkout.js" );
            $(".card-element").show();
            $("#cheque").hide();
        } else if (id == 4) {
            $("#cheque").show();
            $(".card-element").hide();
            $('input[name="cheque_no"]').attr('required', true);
        } else {
            $(".card-element").hide();
            $("#cheque").hide();
        }
    });

    $('input[name="paying_amount"]').on("input", function() {
        $(".change").text(parseFloat( $(this).val() - $('input[name="amount"]').val() ).toFixed({{$general_setting->decimal}}));
    });

    $('input[name="amount"]').on("input", function() {
        if( $(this).val() > parseFloat($('input[name="paying_amount"]').val()) ) {
            alert('Paying amount cannot be bigger than recieved amount');
            $(this).val('');
        }
        else if( $(this).val() > parseFloat($('input[name="balance"]').val()) ) {
            alert('Paying amount cannot be bigger than due amount');
            $(this).val('');
        }
        $(".change").text(parseFloat($('input[name="paying_amount"]').val() - $(this).val()).toFixed({{$general_setting->decimal}}));
    });

    $('select[name="edit_paid_by_id"]').on("change", function() {
        var id = $('select[name="edit_paid_by_id"]').val();
        $('input[name="edit_cheque_no"]').attr('required', false);
        $(".payment-form").off("submit");
        if (id == 3) {
            $(".edit-btn").attr('data-clicked', true);
            $.getScript( "public/vendor/stripe/checkout.js" );
            $(".card-element").show();
            $("#edit-cheque").hide();
        } else if (id == 4) {
            $("#edit-cheque").show();
            $(".card-element").hide();
            $('input[name="edit_cheque_no"]').attr('required', true);
        } else {
            $(".card-element").hide();
            $("#edit-cheque").hide();
        }
    });

    $('input[name="edit_amount"]').on("input", function() {
        if( $(this).val() > parseFloat($('input[name="edit_paying_amount"]').val()) ) {
            alert('Paying amount cannot be bigger than recieved amount');
            $(this).val('');
        }
        $(".change").text(parseFloat($('input[name="edit_paying_amount"]').val() - $(this).val()).toFixed({{$general_setting->decimal}}));
    });

    $('input[name="edit_paying_amount"]').on("input", function() {
        $(".change").text(parseFloat( $(this).val() - $('input[name="edit_amount"]').val() ).toFixed({{$general_setting->decimal}}));
    });

    $('#purchase-table').DataTable( {

        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('purchase-link');
            $(row).attr('data-purchase', data['purchase']);
        },
        "columns": columns,
        'language': {
            /*'searchPlaceholder': "{{trans('file.Type date or purchase reference...')}}",*/
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['1', 'desc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 3, 4, 7, 8, 9, 10]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        purchase_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var purchase = $(this).closest('tr').data('purchase');
                                if(purchase)
                                    purchase_id[i-1] = purchase[3];
                            }
                        });
                        if(purchase_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'purchases/deletebyselection',
                                data:{
                                    purchaseIdArray: purchase_id
                                },
                                success:function(data) {
                                    alert(data);
                                    //dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!purchase_id.length)
                            alert('Nothing is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    });

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 5 ).footer() ).html(dt_selector.column( 5, {page:'current'} ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.column( 6, {page:'current'} ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.column( 7, {page:'current'} ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.column( 8, {page:'current'} ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }
    function purchaseDetails(purchase) {
        var htmltext = `
        <strong>{{trans("file.Date")}}:</strong> ${purchase.date}<br>
        <strong>{{trans("file.reference")}}:</strong> ${purchase.reference_no}<br>
        <strong>{{trans("file.Purchase Status")}}:</strong> ${purchase.purchase_status}<br>
        <strong>{{trans("file.Currency")}}:</strong> ${purchase.currency.code}
    `;

        if (purchase.currency.exchange_rate !== "N/A") {
            htmltext += `<br><strong>{{trans("file.Exchange Rate")}}:</strong> ${purchase.currency.exchange_rate}<br>`;
        } else {
            htmltext += `<br><strong>{{trans("file.Exchange Rate")}}:</strong> N/A<br>`;
        }

        if (purchase.document) {
            htmltext += `<strong>{{trans("file.Attach Document")}}:</strong> <a href="documents/purchase/${purchase.document}">Download</a><br>`;
        }

        htmltext += `
        <br>
        <div class="row">
            <div class="col-md-6">
                <strong>{{trans("file.From")}}:</strong><br>
                ${purchase.supplier.name}<br>
                ${purchase.supplier.company_name}<br>
                ${purchase.supplier.email}<br>
                ${purchase.supplier.phone}<br>
                ${purchase.supplier.address}<br>
                ${purchase.supplier.city}
            </div>
            <div class="col-md-6">
                <div class="float-right">
                    <strong>{{trans("file.To")}}:</strong><br>
                    ${purchase.warehouse.name}<br>
                    ${purchase.warehouse.phone}<br>
                    ${purchase.warehouse.address}
                </div>
            </div>
        </div>
    `;

        $(".product-purchase-list tbody").remove();
        $.get('purchases/product_purchase/' + purchase.id, function(data) {
            var newBody = $("<tbody>");

            if (data == 'Something is wrong!') {
                var newRow = $("<tr>");
                newRow.append('<td colspan="8">Something is wrong!</td>');
                newBody.append(newRow);
            } else {
                // التأكد من أن البيانات تأتي بتنسيق صحيح
                $.each(data, function(index, productPurchase) {
                    var newRow = $("<tr>");
                    newRow.append(`
                <td><strong>${index + 1}</strong></td>
                <td>${productPurchase[0]}</td>  <!-- اسم المنتج ورمزه -->
                <td>${productPurchase[7]}</td>  <!-- رقم الدفعة -->
                <td>${productPurchase[1]} ${productPurchase[2]}</td>  <!-- الكمية + وحدة القياس -->
                <td>${(productPurchase[6] / productPurchase[1]).toFixed(2)}</td>  <!-- السعر للوحدة -->
                <td>${productPurchase[3]} (${productPurchase[4]}%)</td>  <!-- الضريبة -->
                <td>${productPurchase[5]}</td>  <!-- الخصم -->
                <td>${productPurchase[6]}</td>  <!-- المجموع -->
            `);
                    newBody.append(newRow);
                });

                $("table.product-purchase-list").append(newBody);
            }
        });


        var htmlfooter = `
        <p><strong>{{trans("file.Note")}}:</strong> ${purchase.note}</p>
        <strong>{{trans("file.Created By")}}:</strong><br>
        ${purchase.created_by.name}<br>
        ${purchase.created_by.email}
    `;

        $('#purchase-content').html(htmltext);
        $('#purchase-footer').html(htmlfooter);
        $('#purchase-details').modal('show');
    }



    $(document).on('submit', '.payment-form', function(e) {
        if( $('input[name="paying_amount"]').val() < parseFloat($('#amount').val()) ) {
            alert('Paying amount cannot be bigger than recieved amount');
            $('input[name="amount"]').val('');
            $(".change").text(parseFloat( $('input[name="paying_amount"]').val() - $('#amount').val() ).toFixed({{$general_setting->decimal}}));
            e.preventDefault();
        }
        else if( $('input[name="edit_paying_amount"]').val() < parseFloat($('input[name="edit_amount"]').val()) ) {
            alert('Paying amount cannot be bigger than recieved amount');
            $('input[name="edit_amount"]').val('');
            $(".change").text(parseFloat( $('input[name="edit_paying_amount"]').val() - $('input[name="edit_amount"]').val() ).toFixed({{$general_setting->decimal}}));
            e.preventDefault();
        }

        $('#edit-payment select[name="edit_paid_by_id"]').prop('disabled', false);
    });




</script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
