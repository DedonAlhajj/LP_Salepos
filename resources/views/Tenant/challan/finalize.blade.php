@extends('backend.layout.main')
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Finalize Challan</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>The field labels marked with * are required input fields.</small></p>
                        <form action="{{route('challan.update', $challan->id)}}" method="post">
                        @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Reference No</strong> </label>
                                        <p>{{'DC-'.$challan->reference_no}}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Date</strong> </label>
                                        <p>{{date(config('date_format'), strtotime($challan->created_at))}}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Rider/Courier</strong> </label>
                                        <p>{{$challan->courier->name.' ['.$challan->courier->phone_number.']'}}</p>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <h5>Order List</h5>
                                    <?php
                                        $packing_slip_list = explode(",", $challan->packing_slip_list);
                                        $amount_list = explode(",", $challan->amount_list);
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="flat-type-table">
                                            <thead>
                                                <tr>
                                                    <th>PS Reference</th>
                                                    <th>Order Reference</th>
                                                    <th>Amount</th>
                                                    <th>Cash</th>
                                                    <th>Cheque</th>
                                                    <th>Online Payment</th>
                                                    <th>Delivery Charge</th>
                                                    <th class="d-none">Return</th>
                                                    <th class="d-none">Return Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($packing_slip_list as $key=>$packing_slip_id)
                                                <?php
                                                    $packing_slip = \App\Models\PackingSlip::with('sale')->find($packing_slip_id);
                                                ?>
                                                <tr>
                                                    <td>P{{$packing_slip->reference_no}}</td>
                                                    <td>{{$packing_slip->sale->reference_no}}</td>
                                                    <td>{{$amount_list[$key]}}</td>
                                                    <td><input type="number" name="cash_list[]" class="form-control"></td>
                                                    <td><input type="number" name="cheque_list[]" class="form-control"></td>
                                                    <td><input type="number" name="online_payment_list[]" class="form-control"></td>
                                                    <td><input type="number" name="delivery_charge_list[]" class="form-control"></td>
                                                    <td class="d-none"><input type="checkbox" name="refund_list[]" value="{{$packing_slip_id}}"></td>
                                                    <td class="d-none"><input type="textbox" class="form-control" name="return_note[]"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-4">
                                        <input type="submit" value="Submit" class="btn btn-primary">
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
    </script>
@endpush
