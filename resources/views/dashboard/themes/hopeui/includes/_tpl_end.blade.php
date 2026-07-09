<?php $theme_code = 'hopeui' ?>
    <!-- Library Bundle Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/core/libs.min.js') }}"></script>
    <!-- External Library Bundle Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/core/external.min.js') }}"></script>
    <!-- Widgetchart Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/charts/widgetcharts.js') }}"></script>
    <!-- mapchart Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/charts/dashboard.js') }}"></script>
    <!-- fslightbox Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/fslightbox.js') }}"></script>
    <!-- Settings Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/setting.js') }}"></script>
    <!-- Slider-tab Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/slider-tabs.js') }}"></script>
    <!-- Form Wizard Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/form-wizard.js') }}"></script>
    <!-- AOS Animation Plugin-->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/vendor/aos/dist/aos.js') }}"></script>
    <!-- App Script -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/hope-ui.js') }}" defer></script>
@stack('js')

</body>

</html>
