@extends('backend.layout.main')
@section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">Challan Report</h3>
            </div>
            <form action="{{route('report.challan')}}" method="get">
                <div class="row mb-3">
                    <div class="col-md-3 offset-md-1  mt-3">
                        <div class="form-group row">
                            <label class="d-tc mt-2"><strong>Based On</strong> &nbsp;</label>
                            <div class="d-tc">
                                <div class="input-group">
                                    <select name="based_on" class="form-control">
                                        @if($based_on == 'created_at')
                                            <option value="date">Created Date</option>
                                            <option value="closing_date">Closing Date</option>
                                        @else
                                            <option value="created_at">Created Date</option>
                                            <option selected value="closing_date">Closing Date</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-group row">
                            <label class="d-tc mt-2"><strong>Starting Date</strong> &nbsp;</label>
                            <div class="d-tc">
                                <div class="input-group">
                                    <input type="text" class="date form-control" name="starting_date" value="{{$starting_date}}" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-group row">
                            <label class="d-tc mt-2"><strong>Ending Date</strong> &nbsp;</label>
                            <div class="d-tc">
                                <div class="input-group">
                                    <input type="text" class="date form-control" name="ending_date" value="{{$ending_date}}" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mt-5">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table id="challan-table" class="table table-striped">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Challan No</th>
                    <th>Order No</th>
                    <th>Order Date</th>
                    <th>code</th>
                    <th>Delivery Date</th>
                    <th>Sales Amount</th>
                    <th>Cash Payment</th>
                    <th>Online Payment</th>
                    <th>Cheque Payment</th>
                    <th>Shipping Income</th>
                    <th>Delivery Charge</th>
                    <th>Net</th>
                    <th>Net Cash</th>
                </tr>
            </thead>
            <tbody>
                @foreach($challan_data as $challan)
                <?php
                    $packingSlipList = explode(",", $challan->packing_slip_list);
                    $status_list = explode(",", $challan->status_list);
                    $cash_list = explode(",", $challan->cash_list);
                    $cheque_list = explode(",", $challan->cheque_list);
                    $online_payment_list = explode(",", $challan->online_payment_list);
                    $delivery_charge_list = explode(",", $challan->delivery_charge_list);
                ?>
                    @foreach($packingSlipList as  $key => $packingSlipId)
                    <?php $packingSlip = \App\Models\PackingSlip::with('sale.products')->find($packingSlipId); ?>
                    <?php
                        if(!$cash_list[$key])
                            $cash_list[$key] = 0;
                        if(!$online_payment_list[$key])
                            $online_payment_list[$key] = 0;
                        if(!$cheque_list[$key])
                            $cheque_list[$key] = 0;
                        if(!$delivery_charge_list[$key])
                            $delivery_charge_list[$key] = 0;
                    ?>
                    <tr>
                        <td><?php echo $index ?></td>
                        <td>DC-{{$challan->reference_no}}</td>
                        <td>{{$packingSlip->sale->reference_no}}</td>

                        <td>{{date(config('date_format'), strtotime($packingSlip->sale->created_at))}}</td>
                        <td>
                            @foreach($packingSlip->sale->products as $i => $product)
                            @if($i),@endif
                            {{$product->code}}
                            @endforeach
                        </td>
                        <td>
                            @if($packingSlip->sale->sale_status == 1)
                                {{date(config('date_format'), strtotime($packingSlip->sale->updated_at))}}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{$packingSlip->sale->grand_total}}</td>
                        <td>{{$cash_list[$key]}}</td>
                        <td>{{$online_payment_list[$key]}}</td>
                        <td>{{$cheque_list[$key]}}</td>
                        <td>{{$packingSlip->sale->shipping_cost}}</td>
                        <td>{{$delivery_charge_list[$key]}}</td>
                        <td>{{$cash_list[$key] + $online_payment_list[$key] + $cheque_list[$key] - $delivery_charge_list[$key]}}</td>
                        <td>{{$cash_list[$key] - $delivery_charge_list[$key]}}</td>
                    </tr>
                    <?php $index++; ?>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <th></th>
                <th>Total:</th>
                <th></th>
                <th></th>
                <th></th>
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

@endsection

@push('scripts')

    <script type="text/javascript">

        $("ul#report").siblings('a').attr('aria-expanded','true');
        $("ul#report").addClass("show");
        $("ul#report #challan-report-menu").addClass("active");

        $('#challan-table').DataTable( {
            "order": [],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0]
                },
                {
                    'checkboxes': {
                       'selectRow': true
                    },
                    'targets': 0
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
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                    },
                    action: function(e, dt, button, config) {
                        datatable_sum(dt, true);
                        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                        datatable_sum(dt, false);
                    },
                    footer:true
                },
                {
                    extend: 'colvis',
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
                $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 10 ).footer() ).html(dt_selector.cells( rows, 10, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 11 ).footer() ).html(dt_selector.cells( rows, 11, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 12 ).footer() ).html(dt_selector.cells( rows, 12, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 13 ).footer() ).html(dt_selector.cells( rows, 13, { page: 'current' } ).data().sum().toFixed(2));
            }
            else {
                $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 10 ).footer() ).html(dt_selector.cells( rows, 10, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 11 ).footer() ).html(dt_selector.cells( rows, 11, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 12 ).footer() ).html(dt_selector.cells( rows, 12, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 13 ).footer() ).html(dt_selector.cells( rows, 13, { page: 'current' } ).data().sum().toFixed(2));
            }
        }

    </script>
@endpush
