<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{theme_url('images/favicon.png')}}">
    <title>WEBSITE TITLE HERE</title>
    <link href="{{theme_url('css/style.min.css')}}" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

<div id="app">
    <v-app>

    <div class="main-wrapper">
        {{ __('app.login.found') }}<br>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Login box.scss -->
    <!-- ============================================================== -->
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center"
         style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.8)), url({{theme_url('images/background/login-register.jpg')}})
             no-repeat top fixed;
             -webkit-background-size: cover;
             -moz-background-size: cover;
             -o-background-size: cover;
             background-size: cover;
             ">
        <div class="auth-box p-4 bg-white rounded">
            <div id="loginform">
                <div class="logo text-center">
                        <span class="db"><img src="{{theme_url('images/logo-icon.png')}}" alt="logo" /><br/>
                            <img src="{{theme_url('images/logo-text.png')}}" alt="Home" /></span>
                </div>
                <div class="logo">
                    <h3 class="box-title mb-3">ADMIN Sign In</h3>
                </div>
                <!-- Form -->
                <div class="row">
                    <div class="col-12">
                        <login-wrapper
                            login-action-url="{{route('admin.account.login')}}"
                            token-ip-whitelisting="{{$tokenIpWhitelisting}}"
                            token-error-message="{{$tokenErrorMessage}}"
                            reset-loginname-request-url="{{route('admin.account.reset.loginname')}}"
                            reset-password-request-url="{{route('admin.account.reset.password')}}"
                            loginas="ADMIN"
                        >
                        </login-wrapper>

{{--                        <form class="form-horizontal mt-3 form-material" id="loginform" action="index.html">--}}

{{--                            <div class="form-group mb-3">--}}
{{--                                <input class="form-control" type="text" required="" placeholder="Username">--}}
{{--                            </div>--}}
{{--                            <div class="form-group mb-3">--}}
{{--                                <input class="form-control" type="password" required="" placeholder="Password">--}}
{{--                            </div>--}}
{{--                            <div class="form-group mb-3 d-flex">--}}
{{--                                <div class="checkbox checkbox-info float-left pt-0 ml-2 mb-3">--}}
{{--                                    <input id="checkbox-signup" type="checkbox">--}}
{{--                                    <label for="checkbox-signup"> Remember me </label>--}}
{{--                                </div>--}}
{{--                                <a href="javascript:void(0)" id="to-recover" class="text-dark ml-auto mb-3"><i class="fa fa-lock mr-1"></i> Forgot pwd?</a>--}}
{{--                            </div>--}}
{{--                            <div class="form-group text-center">--}}
{{--                                <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>--}}
{{--                            </div>--}}


{{--                            <div class="social mb-3 text-center">--}}
{{--                                <a href="javascript:void(0)" class="btn  btn-facebook" data-toggle="tooltip" title="Login with Facebook"> <i aria-hidden="true" class="fab fa-facebook-f"></i> </a>--}}
{{--                                <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip" title="Login with Google"> <i aria-hidden="true" class="fab fa-google-plus"></i> </a>--}}
{{--                            </div>--}}
{{--                            <div class="form-group mb-0">--}}
{{--                                <div class="text-center">--}}
{{--                                    <p>Don't have an account? <a href="authentication-register1.html" class="text-info font-weight-normal ml-1">Sign Up</a></p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
                    </div>
                </div>
            </div>
            <div id="recoverform">
                <div class="logo">
                    <h3 class="font-weight-medium mb-3">Recover Password</h3>
                    <span>Enter your Email and instructions will be sent to you!</span>
                </div>
                <div class="row mt-3">
                    <!-- Form -->
                    <form class="col-12 form-material" action="index.html">
                        <!-- email -->
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="email" required="" placeholder="Username">
                            </div>
                        </div>
                        <!-- pwd -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button class="btn btn-block btn-lg btn-primary text-uppercase" type="submit" name="action">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Login box.scss -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper scss in scafholding.scss -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper scss in scafholding.scss -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Right Sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Right Sidebar -->
    <!-- ============================================================== -->
</div>
</v-app>
</div>

<script src="{{theme_url('libs/jquery/dist/jquery.min.js')}}"></script>
{{--<!-- Bootstrap tether Core JavaScript -->--}}
{{--<script src="{{theme_url('libs/popper.js/dist/umd/popper.min.js')}}"></script>--}}
{{--<script src="{{theme_url('libs/bootstrap/dist/js/bootstrap.min.js')}}"></script>--}}
{{--<!-- ============================================================== -->--}}
{{--<!-- This page plugin js -->--}}
{{--<!-- ============================================================== -->--}}
<script>
    $( document ).ready(function() {
        $(".preloader").fadeOut();
    });

    // $('[data-toggle="tooltip"]').tooltip();
    // $(".preloader").fadeOut();
    // // ==============================================================
    // // Login and Recover Password
    // // ==============================================================
    // $('#to-recover').on("click", function() {
    //     $("#loginform").slideUp();
    //     $("#recoverform").fadeIn();
    // });
</script>
</body>

</html>
