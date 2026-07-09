<!DOCTYPE html>
@if (app()->getLocale() == 'ar')
    <html direction="rtl" dir="rtl" style="direction: rtl">
@else
    <html lang="en">
@endif

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $settings?->company_name }}" />
    <meta name="keywords" content="{{ $settings?->company_name }}" />
    <meta name="author" content="{{ $settings?->company_name }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon" />
    <title>{{ $settings?->company_name }} | @yield('title')</title>
    <!-- Font -->
<style>
        @font-face {
            font-family: 'Cairo';
        src: url('{{ asset("dashboard/themes/" . $theme_code . "/assets/fonts/Cairo/static/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: 400;
        }

        html,
        body,
        a,
        i,
        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        .btn,
        .alert,
        .dt-button {
            font-family: 'Cairo', sans-serif !important;
        }
    </style>
    <!--- Style css -->
    @if (App::getLocale() == 'en')
        <link href="{{ URL::asset('dashboard/themes/' . $theme_code . '/assets/css/ltr.css') }}" rel="stylesheet">
    @else
        <link href="{{ URL::asset('dashboard/themes/' . $theme_code . '/assets/css/rtl.css') }}" rel="stylesheet">
    @endif
</head>
<body>

    <div class="wrapper">
        <!--================================= preloader -->
        <div id="pre-loader">
            <img src="images/pre-loader/loader-01.svg" alt="">
        </div>

        <!--================================= preloader -->

        <!--================================= login-->
        <section class="height-100vh d-flex align-items-center page-section-ptb login" style="background-image: url('{{ asset('dashboard/themes/' . $theme_code . '/assets/images/login-bg.jpg') }}');">
            <div class="container">
                <div class="row justify-content-center no-gutters vertical-align">
                    <div class="col-lg-4 col-md-6 login-fancy-bg bg"
                        style="background-image: url('{{ asset('dashboard/themes/' . $theme_code . '/assets/images/login-inner-bg') }}');"
                        >
                        <div class="login-fancy">
                            <h2 class="text-white mb-20"></h2>
                            <p class="mb-20 text-white"></p>
                            <ul class="list-unstyled  pos-bot pb-30">
                                <li class="list-inline-item"><a class="text-white" href="#"></a> </li>
                                <li class="list-inline-item"><a class="text-white" href="#"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 bg-white">
                        <div class="login-fancy pb-40 clearfix">
                            <h3 class="mb-30">{{ trans('dashboard/auth.admin_auth_form_title') }}</h3>

                            <form method="POST" action="{{route('admin.post.login')}}">
                                @csrf

                                <div class="section-field mb-20">
                                    <label class="mb-10" for="name">{{ trans('dashboard/auth.email_address') }}</label>
                                    <input id="email" type="email"
                    class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>

                                <div class="section-field mb-20">
                                    <label class="mb-10" for="Password">{{ trans('dashboard/auth.password') }} </label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                                <div class="section-field">
                                    <div class="remember-checkbox mb-30">
                                        <input type="checkbox" class="form-control" name="two" id="two" />
                                        <label for="two">{{ trans('dashboard/auth.remember_me') }}</label>
                                    </div>
                                </div>
                                <button class="button"><span>{{ trans('dashboard/auth.login') }}</span><i class="fa fa-check"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================================= login-->
    </div>
    <!-- jquery -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/jquery-3.3.1.min.js') }}"></script>
    <!-- plugins-jquery -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/plugins-jquery.js') }}"></script>
    <!-- plugin_path -->
    <script>
        var plugin_path = 'js/';
    </script>
    <!-- chart -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/chart-init.js') }}"></script>
    <!-- calendar -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/calendar.init.js') }}"></script>
    <!-- charts sparkline -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/sparkline.init.js') }}"></script>
    <!-- charts morris -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/morris.init.js') }}"></script>
    <!-- datepicker -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/datepicker.js') }}"></script>
    <!-- sweetalert2 -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/sweetalert2.js') }}"></script>
    <!-- toastr -->
    @stack('js')
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/toastr.js') }}"></script>
    <!-- validation -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/validation.js') }}"></script>
    <!-- lobilist -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/lobilist.js') }}"></script>
    <!-- custom -->
    <script src="{{ URL::asset('dashboard/themes/'. $theme_code .'/assets/js/custom.js') }}"></script>
</body>
</html>