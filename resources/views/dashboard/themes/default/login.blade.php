@include('dashboard.themes.' . $theme_code . '.includes.login._tpl_start')
<div class="auth-wrapper auth-v2 ">
    <div class="auth-content">
        @include('dashboard.layouts.common._partials.messages')
        @yield('content')
    </div>
</div>
@include('dashboard.themes.' . $theme_code . '.includes.login._tpl_end')
