@include('dashboard.themes.' . $theme_code . '.includes._tpl_start')
@include('dashboard.themes.' . $theme_code . '.includes._sidebar')
<main class="main-content">
    @include('dashboard.themes.' . $theme_code . '.includes._header')
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        @yield('content')
    </div>
    @include('dashboard.themes.' . $theme_code . '.includes._footer')
</main>
@include('dashboard.themes.' . $theme_code . '.includes._tpl_end')
