<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{url('manifest.json')}}">
    @if(!config('database.connections.saleprosaas_landlord'))
    <link rel="icon" type="image/png" href="" />
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <!-- login stylesheet-->
    <link rel="stylesheet" href="<?php echo asset('css/auth.css') ?>" id="theme-stylesheet" type="text/css">
    <!-- Google fonts - Roboto -->
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet"></noscript>
    @else
    <link rel="icon" type="image/png" href="" />
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <!-- login stylesheet-->
    <link rel="stylesheet" href="<?php echo asset('../../css/auth.css') ?>" id="theme-stylesheet" type="text/css">
    <!-- Google fonts - Roboto -->
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet"></noscript>
    @endif
  </head>
  <body>
    <div class="page login-page">
      <div class="container">
        <div class="form-outer text-center d-flex align-items-center">
          <div class="form-inner">
            <div class="logo">

            </div>
            @if(session()->has('delete_message'))
            <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('delete_message') }}</div>
            @endif
            @if(session()->has('message'))
              <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
            @endif
            @if(session()->has('not_permitted'))
              <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
            @endif
              <form method="POST" action="{{ route('tenant.login.store') }}" id="login-form">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-block">{{ trans('file.LogIn') }}</button>
              </form>
            <p class="register-section">{{trans('file.Do not have an account?')}}</p>
            <a href="{{url('register')}}" class="signup register-section">{{trans('file.Register')}}</a>
          </div>
          <div class="copyrights text-center">
            <p>{{trans('file.Developed By')}} <span class="external"></span></p>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>

<script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.min.js') ?>"></script>


