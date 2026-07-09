    <!-- Required Js -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/jquery.min.js') }}"></script>
    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/main.js') }}"></script>
    <!-- Apex Chart -->
    <script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        var options = {
                series: [{
                    name: 'Inflation',
                    data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2]
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%";
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },

                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    position: 'top',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        fill: {
                            type: 'gradient',
                            gradient: {
                                colorFrom: '#D8E3F0',
                                colorTo: '#BED1E6',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                    }
                },
                yaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: false,
                        formatter: function(val) {
                            return val + "%";
                        }
                    }

                },
                title: {
                    text: 'Monthly Inflation in Argentina, 2002',
                    floating: true,
                    offsetY: 330,
                    align: 'center',
                    style: {
                        color: '#444'
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#traffic-chart"), options);
            chart.render();


            // pie chart
            var options = {
                series: [44, 55, 13, 43, 22],
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#Sales-chart"), options);
            chart.render();
    </script>
    {{--<div class="theme-roller">
        <div class="open-button">
            <button class="btn btn-primary" id="pct-toggler">
                <i data-feather="settings"></i>
            </button>
        </div>
        <div class="theme-roller-content">
            <div class="presets-header bg-primary">
                <h5 class="mb-0 text-white f-w-400">Theme Customizer</h5>
            </div>
            <div class="presets-list">
                <h6 class="mt-2">
                    <i data-feather="credit-card" class="me-2"></i>Primary color settings
                </h6>
                <hr class="my-2" />
                <div class="themes-preference">
                    <a href="#!" class="" data-value="theme-1"></a>
                    <a href="#!" class="" data-value="theme-2"></a>
                    <a href="#!" class="" data-value="theme-3"></a>
                    <a href="#!" class="" data-value="theme-4"></a>
                </div>
                <hr class="my-2" />
                <h6 class="mt-4">
                    <i data-feather="sun" class="me-2"></i>Layout settings
                </h6>
                <hr class="my-2" />
                <div class="mt-2 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="cust-darklayout" />
                    <label class="pl-1 form-check-label f-w-600" for="cust-darklayout">Dark Layout</label>
                </div>
                <h6 class="mt-4">
                    <i data-feather="layout" class="me-2"></i>Right To Left
                </h6>
                <hr class="my-2" />
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="cust-rtllayout" />
                    <label class="pl-1 form-check-label f-w-600" for="cust-rtllayout">RTL layout</label>
                </div>
            </div>
        </div>
    </div>

    <script>
        feather.replace();
            $(document).ready(function() {
                var thtoggle = $("#pct-toggler");
                if (thtoggle.length) {
                    thtoggle.on("click", function() {
                        var themeRoller = $(".theme-roller");
                        if (!themeRoller.hasClass("active")) {
                            themeRoller.addClass("active");
                        } else {
                            themeRoller.removeClass("active");
                        }
                    });
                }

                var themescolors = $(".themes-preference > a");
                themescolors.on("click", function(event) {
                    var targetElement = $(event.target);
                    if (targetElement.is("span")) {
                        targetElement = targetElement.parent();
                    }
                    var temp = targetElement.attr("data-value");
                    $("body").removeClass(function(index, className) {
                        return (className.match(new RegExp("\\btheme-\\S+", "g")) || []).join(" ");
                    });
                    $("body").addClass(temp);
                    localStorage.setItem("themePreference", temp); // Save theme preference color to localStorage
                });

                var custthemebg = $("#cust-rtllayout");
                custthemebg.on("click", function() {
                    if (custthemebg.prop("checked")) {
                        $("html").attr("dir", "rtl");
                        $("html").attr("lang", "ar");
                        $("#rtl-style-link").attr("href", "assets/css/style-rtl.css");
                        localStorage.setItem("rtlLayout", true);
                    } else {
                        $("html").removeAttr("dir");
                        $("html").removeAttr("lang");
                        $("#rtl-style-link").removeAttr("href");
                        localStorage.setItem("rtlLayout", false);
                    }
                });

                var custdarklayout = $("#cust-darklayout");
                custdarklayout.on("click", function() {
                    if (custdarklayout.prop("checked")) {
                        $(".brand-link > .b-brand > .logo-lg").attr("src", "assets/images/logo.svg");
                        $("#main-style-link").attr("href", "assets/css/style-dark.css");
                        localStorage.setItem("darkLayout", true);
                    } else {
                        $(".brand-link > .b-brand > .logo-lg").attr("src", "assets/images/logo-dark.svg");
                        $("#main-style-link").attr("href", "assets/css/style.css");
                        localStorage.setItem("darkLayout", false);
                    }
                });

                function removeClassByPrefix(node, prefix) {
                    $(node).removeClass(function(index, className) {
                        return (className.match(new RegExp("\\b" + prefix + "\\S+", "g")) || []).join(" ");
                    });
                }

                // Load settings from localStorage
                var storedDarkLayout = localStorage.getItem("darkLayout");
                if (storedDarkLayout === "true") {
                    custdarklayout.prop("checked", true);
                    $(".brand-link > .b-brand > .logo-lg").attr("src", "assets/images/logo.svg");
                    $("#main-style-link").attr("href", "assets/css/style-dark.css");
                }

                var storedThemePreference = localStorage.getItem("themePreference");
                if (storedThemePreference) {
                    $("body").removeClass(function(index, className) {
                        return (className.match(new RegExp("\\btheme-\\S+", "g")) || []).join(" ");
                    });
                    $("body").addClass(storedThemePreference);
                }

                // Apply RTL layout on page load
                $(window).on('load', function() {
                    var storedRtlLayout = localStorage.getItem("rtlLayout");
                    if (storedRtlLayout === "true") {
                        custthemebg.prop("checked", true);
                        $("html").attr("dir", "rtl");
                        $("html").attr("lang", "ar");
                        $("#rtl-style-link").attr("href", "assets/css/style-rtl.css");
                    }
                });
            });
    </script>--}}
    @stack('js')
</body>

</html>
