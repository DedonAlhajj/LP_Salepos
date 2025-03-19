<!DOCTYPE html>
<html lang="en">
<head>
    <title>Salepro SaaS Installer | Step-1</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('saas-install-assets/images/favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('saas-install-assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('saas-install-assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('saas-install-assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
	<div class="col-md-6 offset-md-3">
		<div class="wrapper">
	        <header>
	            <img src="{{ asset('saas-install-assets/images/logo.png') }}" alt="Logo"/>
	            <h1 class="text-center">Salepro SaaS  Auto Installer</h1>
	        </header>
            <hr>
            <div class="content text-center">
                <h6>Please <a href="http://codecanyon.net/licenses/standard" target="_blank">Click Here</a> to read the license agreement before installation:</h6>


                <a href="{{ route('saas-install-step-2') }}" class="btn btn-primary">Accept & Continue</a>
                <hr class="mt-lg-5">
                <h6>If you need any help with installation, <br>
                    Please contact <a href="mailto:support@lion-coders.com">support@lion-coders.com</a></h6>
            </div>
            <hr>
            <footer>Copyright &copy; lionCoders. All Rights Reserved.</footer>
		</div>
	</div>
</body>
</html>
