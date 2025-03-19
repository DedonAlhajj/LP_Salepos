<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SalepPro Installer | Step-4</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('saas-install-assets/images/favicon.ico') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{{ asset('saas-install-assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('saas-install-assets/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('saas-install-assets/css/style.css') }}" rel="stylesheet">
    </head>
<body>
    <div class="col-md-6 offset-md-3">
        <div class='wrapper'>
            <header>
                <img src="{{ asset('saas-install-assets/images/logo.png')}}" alt="Logo"/>
                <h1 class="text-center">SalepPro SaaS Auto Installer</h1>
            </header>
            <hr>
            <div class="content pad-top-bot-50">
            <p>
                    <h5><strong class="theme-color">Congratulations!</strong>
                    You have successfully installed SalepPro SaaS.</h5><br>
                    Please  <strong><a href="{{ url('/') }}" target="__blank">Click Here</a></strong> to visit the Landing page of SaaS-
                </p>
            </div>
            <hr>
            <footer>Copyright &copy; lionCoders. All Rights Reserved.</footer>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('saas-install-assets/js/jquery.min.js')}}"></script>
</body>
</html>
