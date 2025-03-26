@extends('Tenant.layout.main') @section('content')
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
    @endif
    @if(session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif

    <section>
        <div class="container-fluid">
            <form action="{{route('challan.create')}}" method="POST" id="challan-form">
                @csrf
                <input type="hidden" name="packing_slip_id">
                <button id="create-challan-btn" type="submit" class="btn btn-info"><i class="fa fa-plus"></i> Create
                    Challan
                </button>
            </form>
        </div>
        <div class="table-responsive">
            <table id="packing-slip-table" class="table table-striped">
                <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{ trans('file.reference') }}</th>
                    <th>{{ trans('file.Sale Reference') }}</th>
                    <th>{{ trans('file.Delivery Reference') }}</th>
                    <th>{{ trans('file.product_list')}}</th>
                    <th>{{ trans('file.Amount') }}</th>
                    <th>{{ trans('file.Status') }}</th>
                    <th>{{ trans('file.Option') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($packingSlips as $packingSlip)
                    <tr>
                        <td></td>
                        <td>{{ $packingSlip['reference'] }}</td>
                        <td>{{ $packingSlip['sale_reference'] }}</td>
                        <td>{{ $packingSlip['delivery_reference'] }}</td>
                        <td>{{ $packingSlip['item_list'] }}</td>
                        <td>{{ $packingSlip['amount'] }}</td>

                        @if($packingSlip['status'] == "In Transit")
                            <td>
                                <div class="badge badge-warning">{!! $packingSlip['status'] !!}</div>
                            </td>
                        @elseif($packingSlip['status'] == "Cancelled")
                            <td>
                                <div class="badge badge-danger"> {!! $packingSlip['status'] !!}</div>
                            </td>
                        @else
                            <td>
                                <div class="badge badge-success">{!! $packingSlip['status'] !!}</div>
                            </td>
                        @endif
                        <td>
                            <div class="btn-group">
                                <a target="_blank" class="btn btn-sm btn-primary" href="{{route('sale.invoice', $packingSlip['packingSlip']->sale->id)}}" title="Generate Invoice">
                                    <i class="dripicons-document-new"></i>
                                </a>&nbsp;&nbsp;
                                <a target="_blank" class="btn btn-sm btn-dark" href="{{route('packingSlip.genInvoice', $packingSlip['id'])}}" title="Generate Shipping Label">
                                    <i class="dripicons-ticket"></i>
                                </a>&nbsp;&nbsp;

                                <form action="{{route('packingSlip.delete', $packingSlip['id'])}}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="dripicons-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endsection

@push('scripts')

    <script type="text/javascript">

        $("ul#sale").siblings('a').attr('aria-expanded', 'true');
        $("ul#sale").addClass("show");
        $("ul#sale #packing-list-menu").addClass("active");

        var packing_slip_id = [];

        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('submit', '#challan-form', function (e) {
            packing_slip_id.length = 0;
            $(':checkbox:checked').each(function (i) {
                if (i) {
                    packing_slip_id[i - 1] = $(this).closest('tr').data('id');
                }
            });
            $("input[name=packing_slip_id]").val(packing_slip_id.toString());
        });

        $('#packing-slip-table').DataTable({

            "createdRow": function (row, data, dataIndex) {
                $(row).attr('data-id', data['id']);
            },
            "columns": [
                {"data": "id"},
                {"data": "reference"},
                {"data": "sale_reference"},
                {"data": "delivery_reference"},
                {"data": "item_list"},
                {"data": "amount"},
                {"data": "status"},
                {"data": "options"},
            ],
            order: [['1', 'desc']],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [2, 3, 4, 5, 6, 7]
                },
                {
                    'render': function (data, type, row, meta) {
                        if (type === 'display') {
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
            'select': {style: 'multi', selector: 'td:first-child'},
            'lengthMenu': [[50, 100, 150], [50, 100, 150]],
            dom: '<"row"lfB>rtip',
            buttons: [
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                    }
                },
                {
                    extend: 'colvis',
                    text: 'Column visibility',
                    columns: ':gt(0)'
                },
            ]
        });
    </script>

@endpush
