@extends('Tenant.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Transfer List')}}</h3>
            </div>
            {!! Form::open(['route' => 'transfers.index', 'method' => 'get']) !!}
            <div class="row mb-3">
                <div class="col-md-3 offset-md-1 mt-3">
                    <div class="d-flex">
                        <label class="">{{trans('file.Date')}} &nbsp;</label>
                        <div class="">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$data['starting_date']}} To {{$data['ending_date']}}" required />
                                <input type="hidden" name="starting_date" value="{{$data['starting_date']}}" />
                                <input type="hidden" name="ending_date" value="{{$data['ending_date']}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="d-flex">
                        <label class="">{{trans('file.From Warehouse')}} &nbsp;</label>
                        <div class="">
                            <select id="from_warehouse_id" name="from_warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($data['lims_warehouse_list'] as $warehouse)
                                    @if($warehouse->id == $data['from_warehouse_id'])
                                        <option selected value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @else
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="d-flex">
                        <label class="">{{trans('file.To Warehouse')}} </label>
                        <div class="">
                            <select id="to_warehouse_id" name="to_warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($data['lims_warehouse_list'] as $warehouse)
                                    @if($warehouse->id == $data['to_warehouse_id'])
                                        <option selected value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @else
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
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
            <a href="{{route('transfers.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Transfer')}}</a>&nbsp;
    </div>
    <div class="table-responsive">
        <table id="transfer-table" class="table transfer-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}} No</th>
                    <th>{{trans('file.Warehouse')}}({{trans('file.From')}})</th>
                    <th>{{trans('file.Warehouse')}}({{trans('file.To')}})</th>
                    <th>{{trans('file.product')}} {{trans('file.Cost')}}</th>
                    <th>{{trans('file.product')}} {{trans('file.Tax')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th>{{trans("file.Status")}}</th>
                    <th>{{trans('file.Email Sent')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($transfers as $key=>$transfer)
                <tr data-transfer='@json($transfer)'>
                    <td>{{ $key}}</td>
                    <td>{{ $transfer->created_at->format(config('date_format')) }}</td>
                    <td>{{ $transfer->reference_no }}</td>
                    <td>{{ $transfer->fromWarehouse->name }}</td>
                    <td>{{ $transfer->toWarehouse->name }}</td>
                    <td>{{ number_format($transfer->total_cost, config('decimal')) }}</td>
                    <td>{{ number_format($transfer->total_tax, config('decimal')) }}</td>
                    <td>{{ number_format($transfer->grand_total, config('decimal')) }}</td>
                    <td>
                        @if ($transfer->status == 1)
                            <div class="badge badge-success">{{ trans('file.Completed') }}</div>
                        @elseif ($transfer->status == 2)
                            <div class="badge badge-danger">{{ trans('file.Pending') }}</div>
                        @elseif ($transfer->status == 3)
                            <div class="badge badge-warning">{{ trans('file.Sent') }}</div>
                        @endif
                    </td>
                    <td>
                        @if ($transfer->is_sent == 1)
                            <div class="badge badge-success">{{ trans('file.Yes') }}</div>
                        @else
                            <div class="badge badge-danger">{{ trans('file.No') }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                {{ trans('file.action') }}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right">
                                <li><button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> {{ trans('file.View') }}</button></li>
                                    <li><a href="{{ route('transfers.edit', $transfer->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{ trans('file.edit') }}</a></li>

                                    <li>
                                        <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{ trans('file.delete') }}</button>
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
            </tfoot>
        </table>
    </div>
</section>

<div id="transfer-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
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
                    <i style="font-size: 15px;">{{trans('file.Transfer Details')}}</i>
                </div>
            </div>
        </div>
            <div id="transfer-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-transfer-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>{{trans('file.Batch No')}}</th>
                    <th>Qty</th>
                    <th>{{trans('file.Unit Cost')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="transfer-footer" class="modal-body"></div>
      </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#transfer").siblings('a').attr('aria-expanded','true');
    $("ul#transfer").addClass("show");
    $("ul#transfer #transfer-list-menu").addClass("active");

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

    var transfer_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

     $(document).on("click", "tr.transfer-link td:not(:first-child, :last-child)", function() {
        var transfers = $(this).parent().data('transfer');
        transferDetails(transfers);
    });

    $(document).on("click", ".view", function() {
        var transfers = $(this).closest("tr").data("transfer");
        transferDetails(transfers);
    });

    $("#print-btn").on("click", function(){
        var divContents = document.getElementById("transfer-details").innerHTML;
        var a = window.open('');
        a.document.write('<html>');
        a.document.write('<body>');
        a.document.write('<style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        setTimeout(function(){a.close();},10);
        a.print();
    });

    var starting_date = $("input[name=starting_date]").val();
    var ending_date = $("input[name=ending_date]").val();
    var from_warehouse_id = $("#from_warehouse_id").val();
    var to_warehouse_id = $("#to_warehouse_id").val();

    $('#transfer-table').DataTable( {

        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('transfer-link');
            $(row).attr('data-transfer', data['transfer']);
        },
        "columns": [
            {"data": "key"},
            {"data": "date"},
            {"data": "reference_no"},
            {"data": "from_warehouse"},
            {"data": "to_warehouse"},
            {"data": "total_cost"},
            {"data": "total_tax"},
            {"data": "grand_total"},
            {"data": "status"},
            {"data": "is_sent"},
            {"data": "options"}
        ],
        'language': {

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
                'targets': [0, 2, 3, 4, 5, 6, 7, 8, 9, 10]
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
        rowId: 'ObjectID',
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
                    columns: ':visible:Not(.not-exported)',
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
                    columns: ':visible:Not(.not-exported)',
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
                    columns: ':visible:Not(.not-exported)',
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
                        transfer_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var transfer = $(this).closest('tr').data('transfer');
                                if(transfer)
                                    transfer_id[i-1] = transfer[3];
                            }
                        });
                        if(transfer_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'transfers/deletebyselection',
                                data:{
                                    transferIdArray: transfer_id
                                },
                                success:function(data){
                                    alert(data);
                                    //dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!transfer_id.length)
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
    } );


    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }
    function transferDetails(transfer) {
        console.log("1");
        var htmltext = `
        <strong>{{trans("file.Date")}}:</strong> ${transfer.created_at} <br>
        <strong>{{trans("file.reference")}}:</strong> ${transfer.reference_no} <br>
        <strong>{{trans("file.Transfer")}} {{trans("file.Status")}}:</strong> ${transfer.status} <br><br>

        <div class="row">
            <div class="col-md-6">
                <strong>{{trans("file.From")}}:</strong><br>
                ${transfer.from_warehouse.name} <br>
            </div>
            <div class="col-md-6">
                <div class="float-right">
                    <strong>{{trans("file.To")}}:</strong><br>
                    ${transfer.to_warehouse.name} <br>
                </div>
            </div>
        </div>
    `;console.log("2");
        console.log("Transfer ID: ", transfer.id);
        $.get('/transfers/product_transfer/' + transfer.id, function(data) {
            try {
                console.log("Received data:", data);

                if (!Array.isArray(data) || data.length === 0) {
                    console.error("Unexpected data format:", data);
                    return;
                }
                console.log("3");
                $(".product-transfer-list tbody").remove();
                var newBody = $("<tbody>");

                var formattedData = data.map((item, index) => ({
                    name_code: item[0] ?? 'N/A',
                    qty: item[1] ?? 0,
                    unit_code: item[2] ?? 'N/A',
                    tax: item[3] ?? 0,
                    tax_rate: item[4] ?? 0,
                    subtotal: item[5] ?? 0,
                    batch_no: item[6] ?? 'N/A'
                }));
                console.log("4");
                $.each(formattedData, function(index, item) {
                    var newRow = $("<tr>");
                    var cols = `
                <td><strong>${index + 1}</strong></td>
                <td>${item.name_code}</td>
                <td>${item.batch_no}</td>
                <td>${item.qty} ${item.unit_code}</td>
                <td>${(item.subtotal / item.qty).toFixed(2)}</td>
                <td>${item.tax} (${item.tax_rate}%)</td>
                <td>${item.subtotal}</td>
            `;
                    newRow.append(cols);
                    newBody.append(newRow);
                });
                console.log("5");
                newBody.append(`
            <tr>
                <td colspan="5"><strong>{{trans("file.Total")}}:</strong></td>
                <td>${transfer.total_tax}</td>
                <td>${transfer.total_cost}</td>
            </tr>
            <tr>
                <td colspan="6"><strong>{{trans("file.Shipping Cost")}}:</strong></td>
                <td>${transfer.shipping_cost}</td>
            </tr>
            <tr>
                <td colspan="6"><strong>{{trans("file.grand total")}}:</strong></td>
                <td>${transfer.grand_total}</td>
            </tr>
        `);
                console.log("6");
                $("table.product-transfer-list").append(newBody);
                console.log("Table updated successfully.");
            } catch (error) {
                console.error("Error processing data:", error);
            }
        });

        console.log("99");

        var htmlfooter = `<p><strong>{{trans("file.Note")}}:</strong> ${transfer.note || 'There No Notes'}</p>
                  <strong>{{trans("file.Created By")}}:</strong><br>
                   ${transfer.user ? transfer.user.name : 'Not valid'}<br>
                  ${transfer.user ? transfer.user.email : 'Not valid'}`;
        console.log("88");
        console.log("Transfer object: ", transfer);
        $('#transfer-content').html(htmltext);
        $('#transfer-footer').html(htmlfooter);
        $('#transfer-details').modal('show');
    }





</script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
