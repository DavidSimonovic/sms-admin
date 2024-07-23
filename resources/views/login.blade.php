<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SMS Admin</title>
    <!-- Favicon icon -->
    <link rel="icon" type="{{URL::asset('image/png')}}" sizes="16x16" href="{{URL::asset('images/favicon.png')}}">
    <link href="{{URL::asset('css/style.css')}}" rel="stylesheet">

</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    @if( Session::has('message'))
                                    <div class="alert alert-success"><strong>Nice!</strong> {{ Session::get('message') }}</div>
                                    @endif        
                                    @if( Session::has('warning'))
                                    <div class="alert alert-danger"><strong>Oops!</strong> {{ Session::get('warning') }}</div>
                                    @endif
                                    <h4 class="text-center mb-4">Sign in to SMS Admin</h4>
                                    <form action="/login" method="post">
                                        <div class="form-group">
                                            <label class="mb-1"><strong>Email</strong></label>
                                            <input name="username" type="text" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1"><strong>Password</strong></label>
                                            <input name="password" type="password" class="form-control" value="">
                                        </div>
                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                               <div class="custom-control custom-checkbox ml-1">
												</div>
                                            </div>
                                            <div class="form-group">
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <input type="hidden" name="_token" value="{{csrf_token()}}">  
                                            <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{URL::asset('vendor/global/global.min.js')}}"></script>
	<script src="{{URL::asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
    <script src="{{URL::asset('js/custom.min.js')}}"></script>
    <script src="{{URL::asset('js/deznav-init.js')}}"></script>

</body>

</html>
<!-- Localized -->