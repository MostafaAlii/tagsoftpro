<title>{{ $settings?->company_name }} | @yield('title')</title>

<!-- Favicon -->
<link rel="shortcut icon" href="{{ URL::asset('dashboard/themes/' . $theme_code . '/assets/images/favicon.ico') }}" type="image/x-icon" />

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
@yield('css')
<!--- Style css -->
<!--- Style css -->
@if (App::getLocale() == 'en')
    <link href="{{ URL::asset('dashboard/themes/' . $theme_code . '/assets/css/ltr.css') }}" rel="stylesheet">
@else
    <link href="{{ URL::asset('dashboard/themes/' . $theme_code . '/assets/css/rtl.css') }}" rel="stylesheet">
@endif
