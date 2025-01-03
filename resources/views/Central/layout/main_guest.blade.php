<!DOCTYPE html>
<html dir="@if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl){{'rtl'}}@endif">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <link rel="icon" type="image/png" href="{{url('../../logo', $general_setting->site_logo)}}" />
  <title>{{$general_setting->site_title}}</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="all,follow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="manifest" href="{{url('manifest.json')}}">
  <!-- Bootstrap CSS-->
  <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-select.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-select.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Font Awesome CSS-->
  <link rel="preload" href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Drip icon font-->
  <link rel="preload" href="<?php echo asset('../../vendor/dripicons/webfont.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/dripicons/webfont.css') ?>" rel="stylesheet">
  </noscript>

  <!-- jQuery Circle-->
  <link rel="preload" href="<?php echo asset('../../css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- Custom Scrollbar-->
  <link rel="preload" href="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" rel="stylesheet">
  </noscript>

  @if(Route::current()->getName() != '/')
  <!-- date range stylesheet-->
  <link rel="preload" href="<?php echo asset('../../vendor/daterange/css/daterangepicker.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/daterange/css/daterangepicker.min.css') ?>" rel="stylesheet">
  </noscript>
  <!-- table sorter stylesheet-->
  <link rel="preload" href="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
  </noscript>
  <link rel="preload" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
  </noscript>
  <link rel="preload" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
  </noscript>
  @endif

  <link rel="stylesheet" href="<?php echo asset('../../css/style.default.css') ?>" id="theme-stylesheet" type="text/css">
  <link rel="stylesheet" href="<?php echo asset('../../css/dropzone.css') ?>">
  <!-- Custom stylesheet - for your changes-->
  <link rel="stylesheet" href="<?php echo asset('../../css/custom-' . $general_setting->theme) ?>" type="text/css" id="custom-style">

  @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
  <!-- RTL css -->
  <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap-rtl.min.css') ?>" type="text/css">
  <link rel="stylesheet" href="<?php echo asset('../../css/custom-rtl.css') ?>" type="text/css" id="custom-style">
  @endif
  <!-- Google fonts - Roboto -->
  <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet">
  </noscript>

  @stack('css')
</head>

<body class="@if($theme == 'dark')dark-mode dripicons-brightness-low @endif  @if(Route::current()->getName() == 'sale.pos') pos-page @endif" onload="myFunction()">
  <div id="loader"></div>
  <!-- Side Navbar -->
  <nav class="side-navbar d-print-none">
    <span class="brand-big">
      @if($general_setting->site_logo)
      <a href="{{url('/')}}"><img src="{{url('../../logo', $general_setting->site_logo)}}" width="115"></a>
      @else
      <a href="{{url('/')}}">
        <h1 class="d-inline">{{$general_setting->site_title}}</h1>
      </a>
      @endif
    </span>
    @include('Central.layout.sidebar')
  </nav>

  <div class="page">
    <!-- navbar-->
    @if(Route::current()->getName() != 'sale.pos')
    <header class="container-fluid">
      <nav class="navbar">
        <a id="toggle-btn" href="#"><i> </i></a>

        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
        @if (Auth::guard('super_users')->user())
        <li class="nav-item">
              <a rel="nofollow" data-toggle="tooltip" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span>{{ucfirst(Auth::guard('super_users')->user()->name)}}</span> <i class="fa fa-angle-down"></i>
              </a>
              <ul class="right-sidebar">
                <li>
                  <a href="{{route('user.profile', ['id' => Auth::guard('super_users')->user()->id])}}"><i class="dripicons-user"></i> {{trans('file.profile')}}</a>
                </li>

                <li>
                  <a href="{{route('setting.general')}}"><i class="dripicons-gear"></i> {{trans('file.settings')}}</a>
                </li>

                <li>
                  <a href="{{url('my-transactions/'.date('Y').'/'.date('m'))}}"><i class="dripicons-swap"></i> {{trans('file.My Transaction')}}</a>
                </li>

                <li>
                  <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();"><i class="dripicons-power"></i>
                    {{trans('file.logout')}}
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                </li>
              </ul>
            </li>
        @else
        <li class="nav-item"><a class="btn-pos btn-sm" href="{{route('login')}}"><i class="fa fa-user"></i><span> LOGIN</span></a></li>
        @endif

      </ul>
      </nav>
    </header>
    @endif


    <div style="display:none" id="content" class="animate-bottom">
      @yield('content')
    </div>

    <footer class="main-footer">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <p>&copy; {{$general_setting->site_title}} | {{trans('file.Developed')}} {{trans('file.By')}} <span class="external">{{$general_setting->developed_by}}</span> | V {{env('VERSION')}}</p>
          </div>
        </div>
      </div>
    </footer>

  </div>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery-ui.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/bootstrap-datepicker.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.timepicker.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/popper.js/umd/popper.min.js') ?>">
  </script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/bootstrap/js/bootstrap-select.min.js') ?>"></script>

  <script type="text/javascript" src="<?php echo asset('../../js/grasp_mobile_progress_circle-1.0.0.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery.cookie/jquery.cookie.js') ?>">
  </script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/chart.js/Chart.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../js/charts-custom.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') ?>"></script>
  @if( Config::get('app.locale') == 'ar' || $general_setting->is_rtl)
  <script type="text/javascript" src="<?php echo asset('../../js/front_rtl.js') ?>"></script>
  @else
  <script type="text/javascript" src="<?php echo asset('../../js/front.js') ?>"></script>
  @endif

  @if(Route::current()->getName() != '/')
  <script type="text/javascript" src="<?php echo asset('../../vendor/daterange/js/moment.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/daterange/js/knockout-3.4.2.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/daterange/js/daterangepicker.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/tinymce/js/tinymce/tinymce.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../js/dropzone.js') ?>"></script>

  <!-- table sorter js-->
  @if( Config::get('app.locale') == 'ar')
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/pdfmake_arabic.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/vfs_fonts_arabic.js') ?>"></script>
  @else
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/pdfmake.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/vfs_fonts.js') ?>"></script>
  @endif
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/jquery.dataTables.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.bootstrap4.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.buttons.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/jszip.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.bootstrap4.min.js') ?>">
    ">
  </script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.colVis.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.html5.min.js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/buttons.printnew.js') ?>"></script>

  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/sum().js') ?>"></script>
  <script type="text/javascript" src="<?php echo asset('../../vendor/datatable/dataTables.checkboxes.min.js') ?>"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
  @endif

  @stack('scripts')
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/salepro/service-worker.js').then(function(registration) {
          // Registration was successful
          console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
          // registration failed :(
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  </script>
  <script type="text/javascript">
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var theme = <?php echo json_encode($theme); ?>;
    if (theme == 'dark') {
      $('body').addClass('dark-mode');
      $('#switch-theme i').addClass('dripicons-brightness-low');
    } else {
      $('body').removeClass('dark-mode');
      $('#switch-theme i').addClass('dripicons-brightness-max');
    }
    $('#switch-theme').click(function() {
      if (theme == 'light') {
        theme = 'dark';
        var url = <?php echo json_encode(route('switchTheme', 'dark')); ?>;
        $('body').addClass('dark-mode');
        $('#switch-theme i').addClass('dripicons-brightness-low');
      } else {
        theme = 'light';
        var url = <?php echo json_encode(route('switchTheme', 'light')); ?>;
        $('body').removeClass('dark-mode');
        $('#switch-theme i').addClass('dripicons-brightness-max');
      }

      $.get(url, function(data) {
        console.log('theme changed to ' + theme);
      });
    });


    if ($(window).outerWidth() > 1199) {
      $('nav.side-navbar').removeClass('shrink');
    }

    function myFunction() {
      setTimeout(showPage, 100);
    }

    function showPage() {
      document.getElementById("loader").style.display = "none";
      document.getElementById("content").style.display = "block";
    }

    $("div.alert").delay(4000).slideUp(800);

    function confirmDelete() {
      if (confirm("Are you sure want to delete?")) {
        return true;
      }
      return false;
    }


    $("a#add-expense").click(function(e) {
      e.preventDefault();
      $('#expense-modal').modal();
    });

    $("a#add-income").click(function(e) {
      e.preventDefault();
      $('#income-modal').modal();
    });

    $("a#send-notification").click(function(e) {
      e.preventDefault();
      $('#notification-modal').modal();
    });

    $("a#add-account").click(function(e) {
      e.preventDefault();
      $('#account-modal').modal();
    });

    $("a#account-statement").click(function(e) {
      e.preventDefault();
      $('#account-statement-modal').modal();
    });

    $("a#profitLoss-link").click(function(e) {
      e.preventDefault();
      $("#profitLoss-report-form").submit();
    });

    $("a#report-link").click(function(e) {
      e.preventDefault();
      $("#product-report-form").submit();
    });

    $("a#purchase-report-link").click(function(e) {
      e.preventDefault();
      $("#purchase-report-form").submit();
    });

    $("a#sale-report-link").click(function(e) {
      e.preventDefault();
      $("#sale-report-form").submit();
    });
    $("a#sale-report-chart-link").click(function(e) {
      e.preventDefault();
      $("#sale-report-chart-form").submit();
    });

    $("a#payment-report-link").click(function(e) {
      e.preventDefault();
      $("#payment-report-form").submit();
    });

    $("a#warehouse-report-link").click(function(e) {
      e.preventDefault();
      $('#warehouse-modal').modal();
    });

    $("a#user-report-link").click(function(e) {
      e.preventDefault();
      $('#user-modal').modal();
    });

    $("a#biller-report-link").click(function(e) {
      e.preventDefault();
      $('#biller-modal').modal();
    });

    $("a#customer-report-link").click(function(e) {
      e.preventDefault();
      $('#customer-modal').modal();
    });

    $("a#customer-group-report-link").click(function(e) {
      e.preventDefault();
      $('#customer-group-modal').modal();
    });

    $("a#supplier-report-link").click(function(e) {
      e.preventDefault();
      $('#supplier-modal').modal();
    });

    $("a#due-report-link").click(function(e) {
      e.preventDefault();
      $("#customer-due-report-form").submit();
    });

    $("a#supplier-due-report-link").click(function(e) {
      e.preventDefault();
      $("#supplier-due-report-form").submit();
    });

    $(".account-statement-daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period) {
        var start_date = startDate.format('YYYY-MM-DD');
        var end_date = endDate.format('YYYY-MM-DD');
        var title = start_date + ' To ' + end_date;
        $(this).val(title);
        $('#account-statement-modal input[name="start_date"]').val(start_date);
        $('#account-statement-modal input[name="end_date"]').val(end_date);
      }
    });

    $('.date').datepicker({
      format: "dd-mm-yyyy",
      autoclose: true,
      todayHighlight: true
    });

    $('.selectpicker').selectpicker({
      style: 'btn-link',
    });


  </script>
</body>

</html>
