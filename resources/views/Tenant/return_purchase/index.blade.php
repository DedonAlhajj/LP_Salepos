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
                <h3 class="text-center">{{trans('file.Purchase Return List')}}</h3>
            </div>
            {!! Form::open(['route' => 'return-purchase.index', 'method' => 'get']) !!}
            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="d-flex">
                        <label class="">{{trans('file.Date')}} &nbsp;</label>
                        <div class="">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$starting_date}} To {{$ending_date}}" required />
                                <input type="hidden" name="starting_date" value="{{$starting_date}}" />
                                <input type="hidden" name="ending_date" value="{{$ending_date}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="d-flex">
                        <label class="">{{trans('file.Warehouse')}} &nbsp;</label>
                        <div class="">
                            <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($lims_warehouse_list as $warehouse)
                                    @if($warehouse->id == $warehouse_id)
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
            <a href="#" data-toggle="modal" data-target="#add-purchase-return" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Return')}}</a>
    </div>
    <div class="table-responsive">
        <table id="return-table" class="table return-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Purchase Reference')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.Supplier')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data as $returns)
                <tr data-return='@json($returns['return'])'>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $returns['date'] }}</td>
                    <td>{{ $returns['reference_no'] }}</td>
                    <td>{{ $returns['purchase_reference'] }}</td>
                    <td>{{ $returns['warehouse'] }}</td>
                    <td>{{ $returns['supplier'] }}</td>
                    <td>{{ $returns['grand_total'] }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans('file.action') }}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" class="btn btn-link view" data-return='@json($returns['return'])'>
                                        <i class="fa fa-eye"></i> {{ trans('file.View') }}
                                    </button>
                                </li>
                                <li>
                                    <a href="{{ route('return-purchase.edit', $returns['id']) }}" class="btn btn-link">
                                        <i class="dripicons-document-edit"></i> {{ trans('file.edit') }}
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('return-purchase.destroy', $returns['id']) }}" method="POST" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link">
                                            <i class="dripicons-trash"></i> {{ trans("file.delete") }}
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
            </tfoot>
        </table>
    </div>
</section>

<div id="return-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
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
                <i style="font-size: 15px;">{{trans('file.Purchase Return Details')}}</i>
            </div>
        </div>
    </div>
            <div id="return-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-return-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>{{trans('file.Batch No')}}</th>
                    <th>{{trans('file.Qty')}}</th>
                    <th>{{trans('file.Unit Price')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Discount')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="return-footer" class="modal-body"></div>
      </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#return").siblings('a').attr('aria-expanded','true');
    $("ul#return").addClass("show");
    $("ul#return #purchase-return-menu").addClass("active");

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

    var return_id = [];
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

    $(document).on("click", "tr.return-link td:not(:first-child, :last-child)", function() {
        var returns = $(this).parent().data('return');
        returnDetails(returns);
    });

    $(document).on("click", ".view", function() {
        var returns = $(this).parent().parent().parent().parent().parent().data('return');
        returnDetails(returns);
    });

    $("#print-btn").on("click", function(){
        var divContents = document.getElementById("return-details").innerHTML;
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
    var warehouse_id = $("#warehouse_id").val();

    $('#return-table').DataTable( {
        "createdRow": function( row, data, dataIndex ) {
            //alert(data);
            $(row).addClass('return-link');
            $(row).attr('data-return', data['return']);
        },
        "columns": [
            {"data": "key"},
            {"data": "date"},
            {"data": "reference_no"},
            {"data": "purchase_reference"},
            {"data": "warehouse"},
            {"data": "supplier"},
            {"data": "grand_total"},
            {"data": "options"},
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
                'targets': [0, 3, 4, 5, 6, 7]
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
                        return_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var returnData = $(this).closest('tr').data('return');
                                return_id[i-1] = returnData[11];
                            }
                        });
                        if(return_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'return-purchase/deletebyselection',
                                data:{
                                    returnIdArray: return_id
                                },
                                success:function(data){
                                    alert(data);
                                    //dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!return_id.length)
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

            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }
    function returnDetails(returns){
        $('input[name="return_id"]').val(returns.id);
        console.log(returns);

        var htmltext = `<strong>{{trans("file.Date")}}: </strong>${returns.date}<br>
                    <strong>{{trans("file.Reference")}}: </strong>${returns.reference_no}<br>
                    <strong>{{trans("file.Purchase Reference")}}: </strong>${returns.purchase_reference}<br>
                    <strong>{{trans("file.Currency")}}: </strong>${returns.currency}<br>`;

        if (returns.exchange_rate)
            htmltext += `<br><strong>{{trans("file.Exchange Rate")}}: </strong>${returns.exchange_rate}<br>`;
        else
            htmltext += `<br><strong>{{trans("file.Exchange Rate")}}: </strong>N/A<br>`;

        htmltext += `<br><div class="row">
                    <div class="col-md-6">
                        <strong>{{trans("file.From")}}:</strong><br>
                        ${returns.warehouse.name}<br>
                        ${returns.warehouse.phone}<br>
                        ${returns.warehouse.address}
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            <strong>{{trans("file.To")}}:</strong><br>
                            ${returns.supplier.name}<br>
                            ${returns.supplier.company_name}<br>
                            ${returns.supplier.email}<br>
                            ${returns.supplier.phone}<br>
                            ${returns.supplier.address}<br>
                            ${returns.supplier.city}
                        </div>
                    </div>
                </div>`;

        $.get('return-purchase/product_return/' + returns.id, function(data) {
            console.log(returns.id);
            $(".product-return-list tbody").empty();
            let newBody = $("<tbody>");

            data.forEach((item, index) => {
                let newRow = $("<tr>");
                newRow.append(`<td><strong>${index + 1}</strong></td>`);
                newRow.append(`<td>${item.name_code} ${item.imei_number}</td>`);
                newRow.append(`<td>${item.batch_no}</td>`);
                newRow.append(`<td>${item.qty} ${item.unit_code}</td>`);
                newRow.append(`<td>${(item.subtotal / item.qty).toFixed(2)}</td>`);
                newRow.append(`<td>${item.tax} (${item.tax_rate}%)</td>`);
                newRow.append(`<td>${item.discount}</td>`);
                newRow.append(`<td>${item.subtotal}</td>`);
                newBody.append(newRow);
            });

            var totalsRow = $("<tr>");
            totalsRow.append(`<td colspan=5><strong>{{trans("file.Total")}}:</strong></td>`);
            totalsRow.append(`<td>${returns.total_tax}</td>`);
            totalsRow.append(`<td>${returns.total_discount}</td>`);
            totalsRow.append(`<td>${returns.grand_total}</td>`);
            newBody.append(totalsRow);

            $("table.product-return-list").append(newBody);
        });

        var htmlfooter = `<p><strong>{{trans("file.Return Note")}}:</strong> ${returns.return_note}</p>
                      <p><strong>{{trans("file.Staff Note")}}:</strong> ${returns.staff_note}</p>
                      <strong>{{trans("file.Created By")}}:</strong><br>
                      ${returns.user.name}<br>
                      ${returns.user.email}`;

        $('#return-content').html(htmltext);
        $('#return-footer').html(htmlfooter);
        $('#return-details').modal('show');
    }

    function returnDetailsm(returns){
        $('input[name="return_id"]').val(returns[11]);
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+returns[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+returns[1]+'<br><strong>{{trans("file.Purchase Reference")}}: </strong>'+returns[22]+'<br><strong>{{trans("file.Currency")}}: </strong>'+returns[24];
        if(returns[25])
            htmltext += '<br><strong>{{trans("file.Exchange Rate")}}: </strong>'+returns[25]+'<br>';
        else
            htmltext += '<br><strong>{{trans("file.Exchange Rate")}}: </strong>N/A<br>';
        if(returns[23])
            htmltext += '<strong>{{trans("file.Attach Document")}}: </strong><a href="documents/purchase_return/'+returns[23]+'">Download</a><br>';
        htmltext += '<br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>'+returns[2]+'<br>'+returns[3]+'<br>'+returns[4]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+returns[5]+'<br>'+returns[6]+'<br>'+returns[7]+'<br>'+returns[8]+'<br>'+returns[9]+', '+returns[10]+'</div></div></div>';
        $.get('return-purchase/product_return/' + returns[11], function(data){
            $(".product-return-list tbody").remove();
            var name_code = data[0];
            var qty = data[1];
            var unit_code = data[2];
            var tax = data[3];
            var tax_rate = data[4];
            var discount = data[5];
            var subtotal = data[6];
            var batch_no = data[7];
            var newBody = $("<tbody>");
            $.each(name_code, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + name_code[index] + '</td>';
                cols += '<td>' + batch_no[index] + '</td>';
                cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';
                cols += '<td>' + (subtotal[index] / qty[index]) + '</td>';
                cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';
                cols += '<td>' + discount[index] + '</td>';
                cols += '<td>' + subtotal[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=5><strong>{{trans("file.Total")}}:</strong></td>';
            cols += '<td>' + returns[12] + '</td>';
            cols += '<td>' + returns[13] + '</td>';
            cols += '<td>' + returns[14] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=7><strong>{{trans("file.Order Tax")}}:</strong></td>';
            cols += '<td>' + returns[15] + '(' + returns[16] + '%)' + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=7><strong>{{trans("file.grand total")}}:</strong></td>';
            cols += '<td>' + returns[17] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            $("table.product-return-list").append(newBody);
        });
        var htmlfooter = '<p><strong>{{trans("file.Return Note")}}:</strong> '+returns[18]+'</p><p><strong>{{trans("file.Staff Note")}}:</strong> '+returns[19]+'</p><strong>{{trans("file.Created By")}}:</strong><br>'+returns[20]+'<br>'+returns[21];
        $('#return-content').html(htmltext);
        $('#return-footer').html(htmlfooter);
        $('#return-details').modal('show');
    }


</script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
