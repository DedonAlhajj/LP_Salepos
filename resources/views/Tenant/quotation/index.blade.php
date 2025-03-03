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
                <h3 class="text-center">{{trans('file.Quotation List')}}</h3>
            </div>
            {!! Form::open(['route' => 'quotations.index', 'method' => 'get']) !!}
            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Your Date')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$data['starting_date']}} To {{$data['ending_date']}}" required />
                                <input type="hidden" name="starting_date" value="{{$data['starting_date']}}" />
                                <input type="hidden" name="ending_date" value="{{$data['ending_date']}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Warehouse')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($data['warehouses'] as $warehouse)
                                    @if($warehouse->id == $data['warehouse_id'])
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
            <a href="{{route('quotations.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Quotation')}}</a>&nbsp;
    </div>
    <div class="table-responsive">
        <table id="quotation-table" class="table quotation-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.Biller')}}</th>
                    <th>{{trans('file.customer')}}</th>
                    <th>{{trans('file.Supplier')}}</th>
                    <th>{{trans('file.Quotation Status')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
                <tbody>
                @foreach ($quotations as $key=>$quotation)
                    <tr data-quotation="{{ json_encode($quotation) }}">
                        <td>{{$key}}</td>
                        <td>{{ $quotation->created_at->format('Y-m-d') }}</td>
                        <td>{{ $quotation->reference_no }}</td>
                        <td>{{ $quotation->warehouse->name ?? '-' }}</td>
                        <td>{{ $quotation->biller->name ?? '-' }}</td>
                        <td>{{ $quotation->customer->name ?? '-' }}</td>
                        <td>{{ $quotation->supplier->name ?? '-' }}</td>
                        <td>
                            @if ($quotation->quotation_status == 1)
                                <span class="badge badge-success">{{ trans('file.Completed') }}</span>
                            @elseif ($quotation->quotation_status == 2)
                                <span class="badge badge-warning">{{ trans('file.Pending') }}</span>
                            @else
                                <span class="badge badge-danger">{{ trans('file.Cancelled') }}</span>
                            @endif
                        </td>
                        <td>{{ number_format($quotation->grand_total, 2) }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ trans("file.action") }}
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    <li>
                                        <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> {{ trans('file.View') }}</button>
                                    </li>
                                    <li>
                                        <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{ trans('file.edit') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('quotation.create_sale', $quotation->id) }}" class="btn btn-link"><i class="fa fa-shopping-cart"></i> {{ trans('file.Create Sale') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('quotation.create_purchase', $quotation->id) }}" class="btn btn-link"><i class="fa fa-shopping-basket"></i> {{ trans('file.Create Purchase') }}</a>
                                    </li>
                                    <li>
                                        <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link"><i class="dripicons-trash"></i> {{ trans('file.delete') }}</button>
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

<div id="quotation-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                <div class="col-md-6 d-print-none">
                    <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                    {{ Form::open(['route' => 'quotation.sendmail', 'method' => 'post', 'class' => 'sendmail-form'] ) }}
                        <input type="hidden" name="quotation_id">
                        <button class="btn btn-default btn-sm d-print-none"><i class="dripicons-mail"></i> {{trans('file.Email')}}</button>
                    {{ Form::close() }}
                </div>

                <div class="col-md-6 d-print-none">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="col-md-12">
                    <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                </div>
                <div class="col-md-12 text-center">
                    <i style="font-size: 15px;">{{trans('file.Quotation Details')}}</i>
                </div>
            </div>
        </div>
            <div id="quotation-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-quotation-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>{{trans('file.Batch No')}}</th>
                    <th>Qty</th>
                    <th>{{trans('file.Unit Price')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Discount')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="quotation-footer" class="modal-body"></div>
      </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#quotation").siblings('a').attr('aria-expanded','true');
    $("ul#quotation").addClass("show");
    $("ul#quotation #quotation-list-menu").addClass("active");

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

    var quotation_id = [];
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

    $(document).on("click", "tr.quotation-link td:not(:first-child, :last-child)", function() {
        var quotation = $(this).parent().data('quotation');
        quotationDetails(quotation);
    });

    $(document).on("click", ".view", function() {
        var quotation = $(this).closest("tr").data("quotation");
        quotationDetails(quotation);
    });


    $("#print-btn").on("click", function(){
        var divContents = document.getElementById("quotation-details").innerHTML;
        var a = window.open('');
        a.document.write('<html>');
        a.document.write('<body><style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        setTimeout(function(){a.close();},10);
        a.print();
    });

    var starting_date = $("input[name=starting_date]").val();
    var ending_date = $("input[name=ending_date]").val();
    var warehouse_id = $("#warehouse_id").val();
    $('#quotation-table').DataTable( {
        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('quotation-link');
            $(row).attr('data-quotation', data['quotation']);
        },
        "columns": [
        ],
        'language': {
            /*'searchPlaceholder': "{{trans('file.Type date or quotation reference...')}}",*/
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
                'targets': [0, 3, 4, 7, 8,9]
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
                        quotation_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var quotation = $(this).closest('tr').data('quotation');
                                quotation_id[i-1] = quotation[13];
                            }
                        });
                        if(quotation_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'quotations/deletebyselection',
                                data:{
                                    quotationIdArray: quotation_id
                                },
                                success:function(data) {
                                    alert(data);
                                    //dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!quotation_id.length)
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

            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }


    function quotationDetails(quotation) {
        console.log(quotation.id);
        $('input[name="quotation_id"]').val(quotation.id);

        var htmltext = `<strong>{{trans("file.Date")}}: </strong>${quotation.created_at}<br>
                    <strong>{{trans("file.reference")}}: </strong>${quotation.reference_no}<br>
                    <strong>{{trans("file.quotation_status")}}: </strong>${quotation.quotation_status}<br>`;


        htmltext += `<br><div class="row">
                    <div class="col-md-6">
                        <strong>{{trans("file.From")}}:</strong><br>
                        ${quotation.biller.name}<br>${quotation.biller.address}<br>
                        ${quotation.biller.phone}<br>${quotation.biller.email}
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            <strong>{{trans("file.To")}}:</strong><br>
                            ${quotation.customer.name}<br>${quotation.customer.address}<br>
                            ${quotation.customer.phone}<br>${quotation.customer.email}
                        </div>
                    </div>
                </div>`;

        $.get(`quotations/product_quotation/${quotation.id}`, function(data) {
            console.log(data.products);
            $(".product-quotation-list tbody").remove();
            var newBody = $("<tbody>");

            $.each(data.products, function(index, product) {
                var newRow = $("<tr>");
                var cols = `
                <td><strong>${index+1}</strong></td>
                <td>${product.name}</td>
                <td>${product.batch_no || '-'}</td>
                <td>${product.qty} ${product.unit_code}</td>
                <td>${(product.subtotal / product.qty).toFixed(2)}</td>
                <td>${product.tax} (${product.tax_rate}%)</td>
                <td>${product.discount}</td>
                <td>${product.subtotal}</td>`;
                newRow.append(cols);
                newBody.append(newRow);
            });
            console.log('Opening modal 1');
            newBody.append(`
            <tr>
                <td colspan=5><strong>{{trans("file.Total")}}:</strong></td>
                <td>${quotation.total_tax}</td>
                <td>${quotation.total_discount}</td>
                <td>${quotation.total_price}</td>
            </tr>
            <tr>
                <td colspan=7><strong>{{trans("file.Order Tax")}}:</strong></td>
                <td>${quotation.order_tax} (${quotation.order_tax_rate}%)</td>
            </tr>
            <tr>
                <td colspan=7><strong>{{trans("file.Order Discount")}}:</strong></td>
                <td>${quotation.order_discount}</td>
            </tr>
            <tr>
                <td colspan=7><strong>{{trans("file.Shipping Cost")}}:</strong></td>
                <td>${quotation.shipping_cost}</td>
            </tr>
            <tr>
                <td colspan=7><strong>{{trans("file.grand total")}}:</strong></td>
                <td>${quotation.grand_total}</td>
            </tr>
        `);
            console.log('Opening modal 3');
            $("table.product-quotation-list").append(newBody);
        });

        var htmlfooter = `<p><strong>{{trans("file.Note")}}:</strong> ${quotation.note || 'There No Notes'}</p>
                  <strong>{{trans("file.Created By")}}:</strong><br>
                  ${quotation.user ? quotation.user.name : 'Not valid'}<br>
                  ${quotation.user ? quotation.user.email : 'Not valid'}`;

        console.log('Opening modal 4');
        $('#quotation-content').html(htmltext);
        $('#quotation-footer').html(htmlfooter);
        console.log('Opening modal');
        $('#quotation-details').modal('show');
    }

    {{--function quotationDetails(quotation){--}}
    {{--    $('input[name="quotation_id"]').val(quotation[13]);--}}
    {{--    var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+quotation[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+quotation[1]+'<br><strong>{{trans("file.Status")}}: </strong>'+quotation[2]+'<br>';--}}
    {{--    if(quotation[25])--}}
    {{--        htmltext += '<strong>{{trans("file.Attach Document")}}: </strong><a href="documents/quotation/'+quotation[25]+'">Download</a><br>';--}}
    {{--    htmltext += '<br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>'+quotation[3]+'<br>'+quotation[4]+'<br>'+quotation[5]+'<br>'+quotation[6]+'<br>'+quotation[7]+'<br>'+quotation[8]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+quotation[9]+'<br>'+quotation[10]+'<br>'+quotation[11]+'<br>'+quotation[12]+'</div></div></div>';--}}
    {{--    $.get('quotations/product_quotation/' + quotation[13], function(data){--}}
    {{--        $(".product-quotation-list tbody").remove();--}}
    {{--        var name_code = data[0];--}}
    {{--        var qty = data[1];--}}
    {{--        var unit_code = data[2];--}}
    {{--        var tax = data[3];--}}
    {{--        var tax_rate = data[4];--}}
    {{--        var discount = data[5];--}}
    {{--        var subtotal = data[6];--}}
    {{--        var batch_no = data[7];--}}
    {{--        var newBody = $("<tbody>");--}}
    {{--        $.each(name_code, function(index){--}}
    {{--            var newRow = $("<tr>");--}}
    {{--            var cols = '';--}}
    {{--            cols += '<td><strong>' + (index+1) + '</strong></td>';--}}
    {{--            cols += '<td>' + name_code[index] + '</td>';--}}
    {{--            cols += '<td>' + batch_no[index] + '</td>';--}}
    {{--            cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';--}}
    {{--            cols += '<td>' + parseFloat(subtotal[index] / qty[index]).toFixed({{$general_setting->decimal}}) + '</td>';--}}
    {{--            cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';--}}
    {{--            cols += '<td>' + discount[index] + '</td>';--}}
    {{--            cols += '<td>' + subtotal[index] + '</td>';--}}
    {{--            newRow.append(cols);--}}
    {{--            newBody.append(newRow);--}}
    {{--        });--}}

    {{--        var newRow = $("<tr>");--}}
    {{--        cols = '';--}}
    {{--        cols += '<td colspan=5><strong>{{trans("file.Total")}}:</strong></td>';--}}
    {{--        cols += '<td>' + quotation[14] + '</td>';--}}
    {{--        cols += '<td>' + quotation[15] + '</td>';--}}
    {{--        cols += '<td>' + quotation[16] + '</td>';--}}
    {{--        newRow.append(cols);--}}
    {{--        newBody.append(newRow);--}}

    {{--        var newRow = $("<tr>");--}}
    {{--        cols = '';--}}
    {{--        cols += '<td colspan=7><strong>{{trans("file.Order Tax")}}:</strong></td>';--}}
    {{--        cols += '<td>' + quotation[17] + '(' + quotation[18] + '%)' + '</td>';--}}
    {{--        newRow.append(cols);--}}
    {{--        newBody.append(newRow);--}}

    {{--        var newRow = $("<tr>");--}}
    {{--        cols = '';--}}
    {{--        cols += '<td colspan=7><strong>{{trans("file.Order Discount")}}:</strong></td>';--}}
    {{--        cols += '<td>' + quotation[19] + '</td>';--}}
    {{--        newRow.append(cols);--}}
    {{--        newBody.append(newRow);--}}

    {{--        var newRow = $("<tr>");--}}
    {{--        cols = '';--}}
    {{--        cols += '<td colspan=7><strong>{{trans("file.Shipping Cost")}}:</strong></td>';--}}
    {{--        cols += '<td>' + quotation[20] + '</td>';--}}
    {{--        newRow.append(cols);--}}
    {{--        newBody.append(newRow);--}}

    {{--        var newRow = $("<tr>");--}}
    {{--        cols = '';--}}
    {{--        cols += '<td colspan=7><strong>{{trans("file.grand total")}}:</strong></td>';--}}
    {{--        cols += '<td>' + quotation[21] + '</td>';--}}
    {{--        newRow.append(cols);--}}
    {{--        newBody.append(newRow);--}}

    {{--        $("table.product-quotation-list").append(newBody);--}}
    {{--    });--}}
    {{--    var htmlfooter = '<p><strong>{{trans("file.Note")}}:</strong> '+quotation[22]+'</p><strong>{{trans("file.Created By")}}:</strong><br>'+quotation[23]+'<br>'+quotation[24];--}}
    {{--    $('#quotation-content').html(htmltext);--}}
    {{--    $('#quotation-footer').html(htmlfooter);--}}
    {{--    $('#quotation-details').modal('show');--}}
    {{--}--}}
</script>
@endpush
