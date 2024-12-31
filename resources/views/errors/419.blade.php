<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Document Title -->
    <title>419 | Page Expired</title>


    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ asset('public/frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- Plugins CSS -->
    <link href="{{ asset('public/frontend/css/plugins.css') }}" rel="stylesheet" />

    <!-- style CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/style.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/style.css') }}">
    </noscript>
    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet" />

</head>
<body>
    <section class="error-section mt-5 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="error-icon">
                        <span style="font-size: 60px;" class="material-symbols-outlined">sentiment_dissatisfied</span>
                    </div>
                </div>
                <div class="col-md-6 offset-md-3  error-text text-center">
                    <i class="las la-binoculars"style="color:var(--theme-color);font-size: 90px;margin-bottom:30px"></i>
                    <h2 class="h1">{{trans('file.Sorry this page is dead!')}}</h2>
                    <p class="lead">{{trans('file.The page is expired due to session expiration. Just refresh the page or hit the button below.') }} <a class="btn-link" href="#">{{trans('file.Refresh')}}</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
