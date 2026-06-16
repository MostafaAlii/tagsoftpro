<!DOCTYPE html>
@if (app()->getLocale() == 'ar')
    <html direction="rtl" dir="rtl" style="direction: rtl">
@else
    <html lang="en">
@endif
<head>
    <title>{{ $settings?->company_name }} | @yield('title')</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{ $settings?->company_name }}" />
    <meta name="keywords" content="{{ $settings?->company_name }}" />
    <meta name="author" content="{{ $settings?->company_name }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon" />

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.4/css/buttons.bootstrap5.min.css">
    <!-- vendor css -->
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/style-rtl.css') }}" id="rtl-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('dashboard/themes/'. $theme_code .'/assets/css/style.css') }}" id="main-style-link">
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
            font-family: 'Cairo', sans-serif;
        }
    </style>
    @stack('css')
</head>

<body class="theme-2">
    <!-- { Pre-loader } start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- { Pre-loader } End -->
