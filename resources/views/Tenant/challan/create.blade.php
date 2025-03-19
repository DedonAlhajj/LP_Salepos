@extends('backend.layout.main')
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Create Challan</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>The field labels marked with * are required input fields.</small></p>
                        <form action="{{route('challan.store')}}" method="post" id="challan-form">
                        @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><strong>Date</strong> </label>
                                        <input type="text" name="created_at" class="form-control date" autocomplete="false">
                                    </div>
                                </div>

                                <div class="col-md-4 courier-section">
                                    <div class="form-group">
                                        <label><strong>Courier *</strong></label>
                                        <select name="courier_id" required id="courier-id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select courier...">
                                            @foreach($courier_list as $courier)
                                            <option value="{{$courier->id}}">{{$courier->name.' ['.$courier->phone_number.']'}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <h5>Order List</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="flat-type-table">
                                            <thead>
                                                <tr>
                                                    <th>sale Reference</th>
                                                    <th>Packing Slip Reference</th>
                                                    <th>Amount</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	@foreach($packing_slip_all as $packing_slip)
                                                <tr>
                                                    <td>{{$packing_slip->sale->reference_no}}</td>
                                                    <td><input type="hidden" name="packing_slip_list[]" value="{{$packing_slip->id}}">P{{$packing_slip->reference_no}}</td>
                                                    <td><input type="number" name="amount_list[]" class="form-control" value="{{$packing_slip->amount}}" readonly></td>
                                                    <td><button class="btn btn-danger btn-sm btnDel" type="button">X</button></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-4">
                                        <input type="submit" value="Submit" class="btn btn-primary submit-btn">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script>
        $("ul#sale").siblings('a').attr('aria-expanded','true');
        $("ul#sale").addClass("show");

        $(document).on('submit', '#challan-form', function(e) {
            $(".submit-btn").prop("disabled", true);
        });

        $("#add-more").on("click", function() {
            var newRow = $("<tr>");
            var cols = '';
            cols += '<td><input type="text" name="sale_no[]" class="form-control"></td>';
            cols += '<td><input type="number" name="amount[]" class="form-control"></td>';
            cols += '<td><button class="btn btn-danger btn-sm btnDel" type="button">X</button></td>';

            newRow.append(cols);
            $("table#flat-type-table tbody").append(newRow);
        });

        //Delete flat type
        $("table#flat-type-table tbody").on("click", ".btnDel", function(event) {
            $(this).closest("tr").remove();
        });
    </script>
@endpush
