@extends('Tenant.layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Return')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'return-sale.store', 'method' => 'post', 'files' => true, 'class' => 'sale-return-form']) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="sale_id" value="{{$sale->id}}">
                                        <h5>{{trans('file.Order Table')}} *</h5>
                                        <div class="table-responsive mt-3">
                                            <table id="myTable" class="table table-hover order-list">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('file.name')}}</th>
                                                        <th>{{trans('file.Code')}}</th>
                                                        <th>{{trans('file.Batch No')}}</th>
                                                        <th>{{trans('file.Quantity')}}</th>
                                                        <th>{{trans('file.Net Unit Price')}}</th>
                                                        <th>{{trans('file.Discount')}}</th>
                                                        <th>{{trans('file.Tax')}}</th>
                                                        <th>{{trans('file.Subtotal')}}</th>
                                                        <th>Choose</th>
                                                    </tr>
                                                <tbody>
                                                @foreach($products as $product)
                                                    <tr>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->code }}</td>
                                                        <td>
                                                            <input type="hidden" class="product-batch-id" name="product_batch_id[]" value="{{ $product->batch_no ?? '' }}">
                                                            {{ $product->batch_no ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="actual_qty[]" class="actual-qty" value="{{ $product->qty }}">
                                                            <input type="number" class="form-control qty" name="qty[]" value="{{ $product->qty }}" required step="any" max="{{ $product->qty }}" />
                                                        </td>
                                                        <td class="net_unit_price">{{ number_format($product->net_unit_price, 2, '.', '') }}</td>
                                                        <td class="discount">{{ number_format($product->discount, 2, '.', '') }}</td>
                                                        <td class="tax">{{ number_format($product->tax, 2, '.', '') }}</td>
                                                        <td class="sub-total">{{ number_format($product->subtotal, 2, '.', '') }}</td>
                                                        <td>
                                                            <input type="checkbox" class="is-return" name="is_return[]" value="{{ $product->id }}">
                                                        </td>

                                                        <!-- القيم المخفية لإرسالها في الفورم -->
                                                        <input type="hidden" class="product-code" name="product_code[]" value="{{ $product->code }}"/>
                                                        <input type="hidden" name="product_id[]" class="product-id" value="{{ $product->id }}"/>
                                                        <input type="hidden" class="unit-price" name="unit_price[]" value="{{ $product->unit_price }}"/> <!-- ✅ تم إضافته هنا -->
                                                        <input type="hidden" name="product_variant_id[]" value="{{ $product->variant_id }}"/>
                                                        <input type="hidden" class="product-price" name="product_price[]" value="{{ $product->product_price }}"/>
                                                        <input type="hidden" class="sale-unit" name="sale_unit[]" value="{{ $product->unit_name }}"/>
                                                        <input type="hidden" class="net_unit_price" name="net_unit_price[]" value="{{ $product->net_unit_price }}" />
                                                        <input type="hidden" class="discount-value" name="discount[]" value="{{ $product->discount }}" />
                                                        <input type="hidden" class="tax-rate" name="tax_rate[]" value="{{ $product->tax_value }}"/>
                                                        <input type="hidden" class="tax-name" value="{{ $product->tax_name ?? 'No Tax' }}" />
                                                        <input type="hidden" class="tax-method" value="{{ $product->tax_method }}"/>
                                                        <input type="hidden" class="unit-tax-value" value="{{ $product->unit_tax_value }}" />
                                                        <input type="hidden" class="tax-value" name="tax[]" value="{{ $product->tax }}" />
                                                        <input type="hidden" class="subtotal-value" name="subtotal[]" value="{{ $product->subtotal }}" />
                                                        <input type="hidden" class="imei-number" name="imei_number[]" value="{{ $product->imei_number }}" />
                                                    </tr>
                                                @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_qty" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_discount" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_price" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="item" />
                                            <input type="hidden" name="order_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="grand_total" />
                                            <input type="hidden" name="change_sale_status" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{trans('file.Order Tax')}}</label>
                                            <select class="form-control" name="order_tax_rate">
                                                <option value="0">No Tax</option>
                                                @foreach($taxes as $tax)
                                                <option value="{{$tax->rate}}">{{$tax->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{trans('file.Attach Document')}}</label>
                                            <i class="dripicons-question" data-toggle="tooltip" title="Only jpg, jpeg, png, gif, pdf, csv, docx, xlsx and txt file is supported"></i>
                                            <input type="file" name="document" class="form-control" />
                                            @if($errors->has('extension'))
                                                <span>
                                                   <strong>{{ $errors->first('extension') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Return Note')}}</label>
                                            <textarea rows="5" class="form-control" name="return_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Staff Note')}}</label>
                                            <textarea rows="5" class="form-control" name="staff_note"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary" id="submit-button">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <table class="table table-bordered table-condensed totals">
            <td><strong>{{trans('file.Items')}}</strong>
                <span class="pull-right" id="item">{{number_format(0, $general_setting->decimal, '.', '')}}</span>
            </td>
            <td><strong>{{trans('file.Total')}}</strong>
                <span class="pull-right" id="subtotal">{{number_format(0, $general_setting->decimal, '.', '')}}</span>
            </td>
            <td><strong>{{trans('file.Order Tax')}}</strong>
                <span class="pull-right" id="order_tax">{{number_format(0, $general_setting->decimal, '.', '')}}</span>
            </td>
            <td><strong>{{trans('file.grand total')}}</strong>
                <span class="pull-right" id="grand_total">{{number_format(0, $general_setting->decimal, '.', '')}}</span>
            </td>
        </table>
    </div>

    <!-- add cash register modal -->
    <div id="cash-register-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
          <div class="modal-content">
            {!! Form::open(['route' => 'cashRegister.store', 'method' => 'post']) !!}
            <div class="modal-header">
              <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Cash Register')}}</h5>
              <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                <div class="row">
                  <div class="col-md-6 form-group warehouse-section">
                      <label>{{trans('file.Warehouse')}} *</strong> </label>
                      <select required name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                          @foreach($warehouses as $warehouse)
                          <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-6 form-group">
                      <label>{{trans('file.Cash in Hand')}} *</strong> </label>
                      <input type="number" name="cash_in_hand" required class="form-control">
                  </div>
                  <div class="col-md-12 form-group">
                      <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                  </div>
                </div>
            </div>
            {{ Form::close() }}
          </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script type="text/javascript">
    $('.sale-return-form').on('submit', function(e) {
        // Flag to track if any checkbox is checked
        var anyCheckboxChecked = false;

        // Iterate through all checkboxes in the form
        $('.is-return').each(function() {
            if ($(this).is(':checked')) {
                anyCheckboxChecked = true;
                return false; // Exit loop if a checked checkbox is found
            }
        });

        // If no checkboxes are checked, show an alert and prevent form submission
        if (!anyCheckboxChecked) {
            alert("Please select at least one product.");
            e.preventDefault(); // Prevent form submission
        }
    });


    $("ul#return").siblings('a').attr('aria-expanded','true');
    $("ul#return").addClass("show");
    $("ul#return #sale-return-menu").addClass("active");
    // array data with selection
    var product_price = [];
    var product_discount = [];
    var tax_rate = [];
    var tax_name = [];
    var tax_method = [];
    var unit_name = [];
    var unit_operator = [];
    var unit_operation_value = [];
    var is_imei = [];

    // temporary array
    var temp_unit_name = [];
    var temp_unit_operator = [];
    var temp_unit_operation_value = [];

    var rowindex;
    var customer_group_rate;
    var row_product_price;
    var role_id = <?php echo json_encode(Auth::user()->role_id) ?>;
    var currency = <?php echo json_encode($currency) ?>;
    var changeSaleStatus;

    $('.selectpicker').selectpicker({
        style: 'btn-link',
    });

    $('[data-toggle="tooltip"]').tooltip();

    //choosing the returned product
    $("#myTable").on("change", ".is-return", function () {
        calculateTotal();
    });

    //Change quantity
    $("#myTable").on('input', '.qty', function() {
        rowindex = $(this).closest('tr').index();
        if($(this).val() < 1 && $(this).val() != '') {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(1);
        alert("Quantity can't be less than 1");
        }
        calculateTotal();
    });

    $('select[name="order_tax_rate"]').on("change", function() {
        calculateGrandTotal();
    });

    function calculateTotal() {
        var total_qty = 0;
        var total_discount = 0;
        var total_tax = 0;
        var total = 0;
        var item = 0;
        changeSaleStatus = 1;
        $(".is-return").each(function(i) {
            if ($(this).is(":checked")) {
                var actual_qty = parseFloat($('table.order-list tbody tr:nth-child(' + (i + 1) + ') .actual-qty').val());
                var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (i + 1) + ') .qty').val());
                if(qty != actual_qty) {
                    changeSaleStatus = 0;
                }
                if(qty > actual_qty) {
                    alert('Quantity can not be bigger than the actual quantity!');
                    qty = actual_qty;
                    $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .qty').val(actual_qty);
                }
                var discount = $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .discount').text();
                var tax = $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .unit-tax-value').val() * qty;
                var unit_price = $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .unit-price').val();

                total_qty += parseFloat(qty);
                total_discount += parseFloat(discount);
                total_tax += parseFloat(tax);
                total += parseFloat(unit_price * qty);
                $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .subtotal-value').val(unit_price * qty);
                $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .sub-total').text(parseFloat(unit_price * qty).toFixed({{$general_setting->decimal}}));
                $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .tax-value').val(parseFloat(tax).toFixed({{$general_setting->decimal}}));
                $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .tax').text(parseFloat(tax).toFixed({{$general_setting->decimal}}));
                item++;
            }
            else {
                changeSaleStatus = 0;
            }
        });

        if(changeSaleStatus)
            $('input[name="change_sale_status"]').val(changeSaleStatus);

        $('input[name="total_qty"]').val(total_qty);
        $('input[name="total_discount"]').val(total_discount.toFixed({{$general_setting->decimal}}));
        $('input[name="total_tax"]').val(total_tax.toFixed({{$general_setting->decimal}}));
        $('input[name="total_price"]').val(total.toFixed({{$general_setting->decimal}}));
        $('input[name="item"]').val(item);
        item += '(' + total_qty + ')';
        $('#item').text(item);

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        var total_qty = parseFloat($('input[name="total_qty"]').val());
        var subtotal = parseFloat($('input[name="total_price"]').val());
        var order_tax = parseFloat($('select[name="order_tax_rate"]').val());
        var order_tax = subtotal * (order_tax / 100);
        var grand_total = subtotal + order_tax;


        $('#subtotal').text(subtotal.toFixed({{$general_setting->decimal}}));
        $('#order_tax').text(order_tax.toFixed({{$general_setting->decimal}}));
        $('input[name="order_tax"]').val(order_tax.toFixed({{$general_setting->decimal}}));
        $('#grand_total').text(grand_total.toFixed({{$general_setting->decimal}}));
        $('input[name="grand_total"]').val(grand_total.toFixed({{$general_setting->decimal}}));
    }

    $(window).keydown(function(e){
        if (e.which == 13) {
            var $targ = $(e.target);
            if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
                var focusNext = false;
                $(this).find(":input:visible:not([disabled],[readonly]), a").each(function(){
                    if (this === e.target) {
                        focusNext = true;
                    }
                    else if (focusNext){
                        $(this).focus();
                        return false;
                    }
                });
                return false;
            }
        }
    });

    $('.sale-return-form').on('submit',function(e){
        var rownumber = $('table.order-list tbody tr:last').index();
        if (rownumber < 0) {
            alert("Please insert product to order table!")
            e.preventDefault();
        }
    });

</script>
@endpush
