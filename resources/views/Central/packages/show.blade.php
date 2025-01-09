@extends('Central.layout.main_guest1')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
 @endif


<div class="row">

    <div class="container-fluid">
    <nav class="navbar"
     style="
         display: flex;
         flex-direction: column;
         align-items: flex-start;
         height: auto;
         width: 100%;
         max-width: 1500px;
         margin: 50px auto;
         padding: 20px 130px;
         border-radius: 10px;
         box-sizing: border-box;
         transition: all 0.3s ease;
         gap: 10px;">
    <a id="toggle-btn" href="#"><i> </i></a>
    <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
        <b>Package Name:</b> {{$package->package_name}}
    </p>
    <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
        <b>Package Duration:</b> {{$package->duration . $package->duration_unit}}
    </p>
    <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
        <b>Package Price:</b> {{$package->price}}
    </p>
    <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
        <b>Package Max_users:</b> {{$package->max_users}}
    </p>
    <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
        <b>Package Max_storage:</b> {{$package->max_storage}}
    </p>
    @if($package->is_active == 1)
        <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
            <b>Package Activation:</b> YES
        </p>
    @else
        <p style="word-wrap: break-word; max-width: 100%; text-align: left;">
            <b>Package Activation:</b> NO
        </p>
    @endif
    <p style="word-wrap: break-word; max-width: 100%; text-align: left; margin-right: 10px;">
        <b>Package Description:</b> {{$package->description}}
    </p>
    @if( Auth::guard('super_users')->user())
        @if(Auth::guard('super_users')->user()->can('is-user'))
        
        <li style="list-style-type: none; position: absolute;
                right: 0;  bottom: 0;  padding: 10px 20px;" class="nav-item"><a class="btn-pos btn-sm" href="{{ route('Central.register.form', ['package' => $package->id]) }}"><i class="fa fa-user"></i><span> REGISTER</span></a></li>

        @else
        <li style="list-style-type: none; position: absolute;
            right: 0;  bottom: 0;  padding: 10px 100px;" class="nav-item"><a class="btn-pos btn-sm" href=""><i class="fa fa-pencil"></i><span> Edit</span></a></li>
        <li style="list-style-type: none; position: absolute;
            right: 0;  bottom: 0;  padding: 10px 10px;" class="nav-item"><a class="btn-pos btn-sm" href=""><i class="fa fa-trash"></i><span> Delete</span></a></li>
        @endif
     @else
     <li style="list-style-type: none; position: absolute;
                right: 0;  bottom: 0;  padding: 10px 20px;" class="nav-item"><a class="btn-pos btn-sm" href="{{ route('Central.register.form', ['package' => $package->id]) }}"><i class="fa fa-user"></i><span> REGISTER</span></a></li>
   @endif
</nav>


    </header>
</div>
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
