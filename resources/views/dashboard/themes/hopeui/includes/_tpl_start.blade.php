<!doctype html>
@if (app()->getLocale() == 'ar')
    <html lang="en" direction="rtl" dir="rtl" style="--bs-info: #079aa2;">
@else
    <html lang="en" dir="ltr" style="--bs-info: #079aa2;">
@endif
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $settings?->company_name }} | @yield('title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ $settings?->company_name }}" />
    <meta name="keywords" content="{{ $settings?->company_name }}" />
    <meta name="author" content="{{ $settings?->company_name }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon" />
    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/vendor/aos/dist/aos.css') }}">
    <!-- Hope Ui Design System Css & Custom Css -->
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/hope-ui.min.css?v=4.0.0') }}">
    
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/custom.min.css?v=4.0.0') }}">
    <!-- Dark Css -->
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/dark.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/customizer.min.css') }}">
    @if (app()->getLocale() == 'ar')
    <!-- RTL Css -->
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/rtl.min.css') }}">
    @endif
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset("dashboard/themes/" . $theme_code . "/assets/fonts/Cairo/static/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: 400;
        }
    
        html,
        body,
        a,
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
    @stack('css')
  </head>
  <body class="light theme-color-default" data-aos-easing="ease" data-aos-duration="700" data-aos-delay="0">
    <!-- loader Start -->
    <div id="loading">
      <div class="loader simple-loader">
          <div class="loader-body">
          </div>
      </div>
    </div>
    <!-- loader END -->
