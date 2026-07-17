@include('dashboard.themes.' . $theme_code . '.includes._tpl_start')
@include('dashboard.admin.sidebar')
<main class="main-content">
    @include('dashboard.themes.' . $theme_code . '.includes._header')
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        @include('dashboard.layouts.common._partials.messages')
        @yield('content')
    </div>
    @include('dashboard.themes.' . $theme_code . '.includes._footer')
</main>
@include('dashboard.themes.' . $theme_code . '.includes._tpl_end')
