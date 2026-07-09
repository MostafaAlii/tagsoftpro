<!DOCTYPE html>
@if (app()->getLocale() == 'ar')
    <html direction="rtl" dir="rtl" style="direction: rtl">
@else
    <html lang="en">
@endif

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ $settings?->company_name }}" />
    <meta name="keywords" content="{{ $settings?->company_name }}" />
    <meta name="author" content="{{ $settings?->company_name }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon" />
    @include('dashboard.themes.' . $theme_code . '.includes.head')
</head>

<body>

    <div class="wrapper">
        <!--================================= preloader -->
        <div id="pre-loader">
            <img src="{{asset('dashboard/themes/'. $theme_code .'/assets/images/pre-loader/loader-01.svg')}}" alt="{{ $settings?->company_name }}">
        </div>
        <!--================================= preloader -->
        @include('dashboard.themes.' . $theme_code . '.includes.main-header')

        @include('dashboard.themes.' . $theme_code . '.includes.main-sidebar')

        <!--================================= Main content -->
        <!-- main-content -->
        <div class="content-wrapper">
            @include('dashboard.layouts.common._partials.messages')
            @yield('content')
            <!--================================= wrapper -->
            <!--================================= footer -->
            @include('dashboard.themes.' . $theme_code . '.includes.footer')
        </div><!-- main content wrapper end-->
    </div>

    <!--=================================
 footer -->

    @include('dashboard.themes.' . $theme_code . '.includes.footer-scripts')

</body>

</html>
