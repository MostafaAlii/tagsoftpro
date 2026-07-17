@include('dashboard.themes.' . $theme_code . '.includes._tpl_start')
@include('dashboard.themes.' . $theme_code . '.includes._header')
@include('dashboard.admin.sidebar')
<!-- [ Main Content ] start -->
<div class="page-content-wrapper">
    <div class="content-container">
        <!-- Start page-content -->
        @include('dashboard.layouts.common._partials.messages')
        @yield('content')
        <!-- End page-content -->
    </div>
</div>
<!-- [ Main Content ] end -->
@include('dashboard.themes.' . $theme_code . '.includes._footer')
@include('dashboard.themes.' . $theme_code . '.includes._tpl_end')
