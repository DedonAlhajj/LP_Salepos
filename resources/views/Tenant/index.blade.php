@extends('Tenant.layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
      @php
        if($general_setting->theme == 'default.css') {
          $color = '#733686';
          $color_rgba = 'rgba(115, 54, 134, 0.8)';
        }
        elseif($general_setting->theme == 'green.css') {
            $color = '#2ecc71';
            $color_rgba = 'rgba(46, 204, 113, 0.8)';
        }
        elseif($general_setting->theme == 'blue.css') {
            $color = '#3498db';
            $color_rgba = 'rgba(52, 152, 219, 0.8)';
        }
        elseif($general_setting->theme == 'dark.css'){
            $color = '#34495e';
            $color_rgba = 'rgba(52, 73, 94, 0.8)';
        }
      @endphp
      <div class="row">

        <div class="container-fluid">

          <div class="col-md-12">
            <div class="brand-text float-left mt-4">
                <h3>{{trans('file.welcome')}} <span>{{Auth::guard('web')->user()->name}}</span></h3>
            </div>
          </div>
        </div>
      </div>
      <!-- Counts Section -->
      <section class="dashboard-counts">
        <div class="container-fluid">
          <div class="row">

            <div class="col-md-7">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>{{trans('file.Recent Transaction')}}</h4>
                  <div class="right-column">
                    <div class="badge badge-primary">{{trans('file.latest')}} 5</div>
                  </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#sale-latest" role="tab" data-toggle="tab">{{trans('file.Sale')}}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#purchase-latest" role="tab" data-toggle="tab">{{trans('file.Purchase')}}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#quotation-latest" role="tab" data-toggle="tab">{{trans('file.Quotation')}}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#payment-latest" role="tab" data-toggle="tab">{{trans('file.Payment')}}</a>
                  </li>
                </ul>

                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                      <div class="table-responsive">
                        <table id="recent-sale" class="table">
                          <thead>
                            <tr>
                              <th>{{trans('file.date')}}</th>
                              <th>{{trans('file.reference')}}</th>
                              <th>{{trans('file.customer')}}</th>
                              <th>{{trans('file.status')}}</th>
                              <th>{{trans('file.grand total')}}</th>
                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                        </table>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="purchase-latest">
                      <div class="table-responsive">
                        <table id="recent-purchase" class="table">
                          <thead>
                            <tr>
                              <th>{{trans('file.date')}}</th>
                              <th>{{trans('file.reference')}}</th>
                              <th>{{trans('file.Supplier')}}</th>
                              <th>{{trans('file.status')}}</th>
                              <th>{{trans('file.grand total')}}</th>
                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                        </table>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="quotation-latest">
                      <div class="table-responsive">
                        <table id="recent-quotation" class="table">
                          <thead>
                            <tr>
                              <th>{{trans('file.date')}}</th>
                              <th>{{trans('file.reference')}}</th>
                              <th>{{trans('file.customer')}}</th>
                              <th>{{trans('file.status')}}</th>
                              <th>{{trans('file.grand total')}}</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="payment-latest">
                      <div class="table-responsive">
                        <table id="recent-payment" class="table">
                          <thead>
                            <tr>
                              <th>{{trans('file.date')}}</th>
                              <th>{{trans('file.reference')}}</th>
                              <th>{{trans('file.Amount')}}</th>
                              <th>{{trans('file.Paid By')}}</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>{{trans('file.Best Seller').' '.date('F')}}</h4>
                  <div class="right-column">
                    <div class="badge badge-primary">{{trans('file.top')}} 5</div>
                  </div>
                </div>
                <div class="table-responsive">
                    <table id="monthly-best-selling-qty" class="table">
                      <thead>
                        <tr>
                          <th>{{trans('file.Product Details')}}</th>
                          <th>{{trans('file.qty')}}</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>{{trans('file.Best Seller').' '.date('Y'). '('.trans('file.qty').')'}}</h4>
                  <div class="right-column">
                    <div class="badge badge-primary">{{trans('file.top')}} 5</div>
                  </div>
                </div>
                <div class="table-responsive">
                    <table id="yearly-best-selling-qty" class="table">
                      <thead>
                        <tr>
                          <th>{{trans('file.Product Details')}}</th>
                          <th>{{trans('file.qty')}}</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>{{trans('file.Best Seller').' '.date('Y') . '('.trans('file.price').')'}}</h4>
                  <div class="right-column">
                    <div class="badge badge-primary">{{trans('file.top')}} 5</div>
                  </div>
                </div>
                <div class="table-responsive">
                    <table id="yearly-best-selling-price" class="table">
                      <thead>
                        <tr>
                          <th>{{trans('file.Product Details')}}</th>
                          <th>{{trans('file.grand total')}}</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </section>


@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
      $.ajax({
        url: '{{url("/yearly-best-selling-price")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var url = '{{url("/public/images/product")}}';
            data.forEach(function(item){
              if(item.product_images)
                var images = item.product_images.split(',');
              else
                var images = ['zummXD2dvAtI.png'];
              $('#yearly-best-selling-price').find('tbody').append('<tr><td><img src="'+url+'/'+images[0]+'" height="25" width="30"> '+item.product_name+' ['+item.product_code+']</td><td>'+item.total_price+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/yearly-best-selling-qty")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var url = '{{url("/public/images/product")}}';
            data.forEach(function(item){
              if(item.product_images)
                var images = item.product_images.split(',');
              else
                var images = ['zummXD2dvAtI.png'];
              $('#yearly-best-selling-qty').find('tbody').append('<tr><td><img src="'+url+'/'+images[0]+'" height="25" width="30"> '+item.product_name+' ['+item.product_code+']</td><td>'+item.sold_qty+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/monthly-best-selling-qty")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var url = '{{url("/public/images/product")}}';
            data.forEach(function(item){
              if(item.product_images)
                var images = item.product_images.split(',');
              else
                var images = ['zummXD2dvAtI.png'];
              $('#monthly-best-selling-qty').find('tbody').append('<tr><td><img src="'+url+'/'+images[0]+'" height="25" width="30"> '+item.product_name+' ['+item.product_code+']</td><td>'+item.sold_qty+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-sale")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var sale_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              if(item.sale_status == 1){
                var status = '<div class="badge badge-success">{{trans("file.Completed")}}</div>';
              } else if(item.sale_status == 2) {
                var status = '<div class="badge badge-danger">{{trans("file.Pending")}}</div>';
              } else {
                var status = '<div class="badge badge-warning">{{trans("file.Draft")}}</div>';
              }
              $('#recent-sale').find('tbody').append('<tr><td>'+sale_date+'</td><td>'+item.reference_no+'</td><td>'+item.name+'</td><td>'+status+'</td><td>'+item.grand_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-purchase")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var payment_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              if(item.status == 1){
                var status = '<div class="badge badge-success">{{trans("file.Recieved")}}</div>';
              }
              else if(item.status == 2) {
                var status = '<div class="badge badge-danger">{{trans("file.Partial")}}</div>';
              }
              else if(item.status == 3) {
                var status = '<div class="badge badge-danger">{{trans("file.Pending")}}</div>';
              }
              else {
                var status = '<div class="badge badge-warning">{{trans("file.Ordered")}}</div>';
              }
              $('#recent-purchase').find('tbody').append('<tr><td>'+payment_date+'</td><td>'+item.reference_no+'</td><td>'+item.name+'</td><td>'+status+'</td><td>'+item.grand_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-quotation")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var quotation_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              if(item.quotation_status == 1){
                var status = '<div class="badge badge-success">{{trans("file.Pending")}}</div>';
              }
              else if(item.quotation_status == 2) {
                var status = '<div class="badge badge-danger">{{trans("file.Sent")}}</div>';
              }
              $('#recent-quotation').find('tbody').append('<tr><td>'+quotation_date+'</td><td>'+item.reference_no+'</td><td>'+item.name+'</td><td>'+status+'</td><td>'+item.grand_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-payment")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var payment_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              $('#recent-payment').find('tbody').append('<tr><td>'+payment_date+'</td><td>'+item.payment_reference+'</td><td>'+item.amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'</td><td>'+item.paying_method+'</td></tr>');
            })
        }
      });
    });

    function dateFormat(inputDate, format) {
        const date = new Date(inputDate);
        //extract the parts of the date
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();
        //replace the month
        format = format.replace("m", month.toString().padStart(2,"0"));
        //replace the year
        format = format.replace("Y", year.toString());
        //replace the day
        format = format.replace("d", day.toString().padStart(2,"0"));
        return format;
    }


    $(document).ready(function(){
      $.ajax({
        url: '{{url("/")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#userShowModal').modal('show');
            $('#user-id').text(data.id);
            $('#user-name').text(data.name);
            $('#user-email').text(data.email);
        }
      });
    })
    // Show and hide color-switcher
    $(".color-switcher .switcher-button").on('click', function() {
        $(".color-switcher").toggleClass("show-color-switcher", "hide-color-switcher", 300);
    });

    // Color Skins
    $('a.color').on('click', function() {
        /*var title = $(this).attr('title');
        $('#style-colors').attr('href', 'css/skin-' + title + '.css');
        return false;*/
        $.get('setting/general_setting/change-theme/' + $(this).data('color'), function(data) {
        });
        var style_link= $('#custom-style').attr('href').replace(/([^-]*)$/, $(this).data('color') );
        $('#custom-style').attr('href', style_link);
    });

    $(".date-btn").on("click", function() {
        $(".date-btn").removeClass("active");
        $(this).addClass("active");
        var start_date = $(this).data('start_date');
        var end_date = $(this).data('end_date');
        var warehouse_id = $("#warehouse_btn").val();
        $.get('dashboard-filter/' + start_date + '/' + end_date + '/' + warehouse_id, function(data) {
            dashboardFilter(data);
        });
    });

    $("#warehouse_btn").on("change", function() {
        var warehouse_id = $(this).val();
        var start_date = $('.date-btn.active').data('start_date');
        var end_date = $('.date-btn.active').data('end_date');
        //console.log(start_date);
        //console.log(end_date);
        $.get('dashboard-filter/' + start_date + '/' + end_date + '/' + warehouse_id, function(data) {
            dashboardFilter(data);
        });
    });

    function dashboardFilter(data){
        $('.revenue-data').hide();
        $('.revenue-data').html(parseFloat(data[0]).toFixed({{$general_setting->decimal}}));
        $('.revenue-data').show(500);

        $('.return-data').hide();
        $('.return-data').html(parseFloat(data[1]).toFixed({{$general_setting->decimal}}));
        $('.return-data').show(500);

        $('.profit-data').hide();
        $('.profit-data').html(parseFloat(data[2]).toFixed({{$general_setting->decimal}}));
        $('.profit-data').show(500);

        $('.purchase_return-data').hide();
        $('.purchase_return-data').html(parseFloat(data[3]).toFixed({{$general_setting->decimal}}));
        $('.purchase_return-data').show(500);
    }
</script>
@endpush
