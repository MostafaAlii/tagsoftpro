@include('dashboard.layouts.common.includes._tpl_start')
@include('dashboard.layouts.common.includes._header')
@include('dashboard.layouts.common.includes._sidebar')
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
@include('dashboard.layouts.common.includes._footer')
@include('dashboard.layouts.common.includes._tpl_end')
