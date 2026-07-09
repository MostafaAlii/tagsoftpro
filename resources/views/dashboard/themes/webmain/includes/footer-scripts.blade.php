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
