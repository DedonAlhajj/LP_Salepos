@extends('backend.layout.main')
@section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">Challan List</h3>
            </div>
            <form action="{{route('challan.index')}}", method="GET">
                <div class="row mb-3 offset-1">
                    <div class="col-md-3 mt-3">
                        <label class="">Courier</label>
                        <select name="courier_id" id="courier-id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select courier...">
                            <option value="All Courier">All Courier</option>
                            @foreach($courier_list as $courier)
                                @if($courier_id == $courier->id)
                                    <option value="{{$courier->id}}" selected>{{$courier->name.' ['.$courier->phone_number.']'}}</option>
                                @else
                                    <option value="{{$courier->id}}">{{$courier->name.' ['.$courier->phone_number.']'}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-3">
                        <label class="">Status</label>
                        <select id="status" name="status" class="selectpicker form-control">
                            <option value="0">All</option>
                            @if($status === 'Active')
                                <option value="Active" selected>Active</option>
                                <option value="Close">Close</option>
                            @elseif($status === 'Close')
                                <option value="Active">Active</option>
                                <option value="Close" selected>Close</option>
                            @else
                                <option value="Active">Active</option>
                                <option value="Close">Close</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2 mt-5">
                        <div class="form-group">
                            <button class="btn btn-primary" id="filter-btn" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table id="challan-data-table" class="table challan-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Date</th>
                    <th>Reference No</th>
                    <th>Order No</th>
                    <th>Courier</th>
                    <th>Status</th>
                    <th>Closing Date</th>
                    <th>Total Amount</th>
                    <th>Created By</th>
                    <th>Closed By</th>
                    <th class="not-exported">Action</th>
                </tr>
            </thead>

            <tfoot class="tfoot active">
                <th></th>
                <th>Total</th>
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

        $("ul#sale").siblings('a').attr('aria-expanded','true');
        $("ul#sale").addClass("show");
        $("ul#sale #challan-list-menu").addClass("active");

        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }

        $("#courier-id").val(<?php echo json_encode($courier_id) ?>);
        var challan_id = [];

        $(document).on('submit', '#challan-deposit-form', function(e) {
            challan_id.length = 0;
            $(':checkbox:checked').each(function(i) {
                if(i){
                    challan_id[i-1] = $(this).closest('tr').data('id');
                }
            });
            $("input[name=challan_id]").val(challan_id.toString());
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var courier_id = $("#courier-id").val();
        var status = $("select[name=status]").val();

        $('#challan-data-table').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax":{
                url:"challans/challan-data",
                data:{
                    courier_id: courier_id,
                    status: status
                },
                dataType: "json",
                type:"post"
            },
            "createdRow": function( row, data, dataIndex ) {
                $(row).attr('data-id', data['id']);
            },
            "columns": [
                {"data": "id"},
                {"data": "date"},
                {"data": "reference"},
                {"data": "sale_reference"},
                {"data": "courier"},
                {"data": "status"},
                {"data": "closing_date"},
                {"data": "total_amount"},
                {"data": "created_by"},
                {"data": "closed_by"},
                {"data": "options"},
            ],
            order:[['2', 'desc']],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [3, 4, 5, 6, 7, 8, 9, 10]
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
            'select': { style: 'multi', selector: 'td:first-child'},
            'lengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]],
            dom: '<"row"lfB>rtip',
            buttons: [
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                    },
                    action: function(e, dt, button, config) {
                        datatable_sum(dt, true);
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                        datatable_sum(dt, false);
                    },
                    footer:true
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
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
                    text: 'Print',
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
                    text: 'Column visibility',
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

                $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }
            else {
                $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }
        }

    </script>
@endpush
