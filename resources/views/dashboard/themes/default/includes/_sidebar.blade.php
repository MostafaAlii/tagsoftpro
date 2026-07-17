<!-- { navigation menu } start -->
<aside class="app-sidebar app-light-sidebar">
    <div class="app-navbar-wrapper">
        <div class="brand-link brand-logo">
            <a href="{{ guard_dashboard_route() }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{ $logo }}" alt="" class="logo logo-lg" width="223" height="35" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="app-navbar">
                <li class="nav-item {{ is_active('admin.dashboard') }}">
                    <a href="{{ guard_dashboard_route() }}" class="nav-link">
                        <span class="nav-icon">
                            <i class="ti ti-layout-2"></i>
                        </span>
                        <span class="nav-text">{{trans('dashboard/header.main_dashboard') }}</span>

                    </a>
                </li>
                <!-- Start AdminPanelSetting -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.mainSettings.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-layout-2"></i></span><span
                            class="nav-text">{{ trans('dashboard/sidebar.admin_main_settings_sidebar_title')
                            }}</span><span class="nav-arrow"><i data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.mainSettings.index') }}"
                                href="{{route('admin.mainSettings.index')}}">{{
                                trans('dashboard/sidebar.main_settings_sidebar_title') }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End AdminPanelSetting -->
                <!-- Start Themes -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.themes.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-palette"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/themes.themes') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.themes.index') }}"
                                href="{{route('admin.themes.index')}}">{{
                                trans('dashboard/themes.themes')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Themes -->
                @ownerOnly
                <!-- Start Departments -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.departments.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-building"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/departments.departments') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.departments.index') }}"
                                href="{{route('admin.departments.index')}}">{{
                                trans('dashboard/departments.departments')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Departments -->
                <!-- Start Employees -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.employees.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-users"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/employees.employees') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.employees.index') }}"
                                href="{{route('admin.employees.index')}}">{{
                                trans('dashboard/employees.employees')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Employees -->
                <!-- Start Client -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.clients.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-award"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/sidebar.admin_client_sidebar_title') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.clients.index') }}"
                                href="{{route('admin.clients.index')}}">{{
                                trans('dashboard/sidebar.client_sidebar_title')
                                }}</a>
                        </li>
                    </ul>
                </li>

                <!-- Start Project Types -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.projectTypes.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-folder"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/project_types.project_types') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.projectTypes.index') }}"
                                href="{{route('admin.projectTypes.index')}}">{{
                                trans('dashboard/project_types.project_types')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Project Types -->
                <!-- Start Modules -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.modules.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-gears"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/modules.modules') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.modules.index') }}"
                                href="{{route('admin.modules.index')}}">{{
                                trans('dashboard/modules.modules')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Modules -->
                <!-- Start Projects -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.projects.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-folder-plus"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/projects.projects') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.projects.index') }}"
                                href="{{route('admin.projects.index')}}">{{
                                trans('dashboard/projects.projects')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Projects -->
                <li class="nav-item nav-hasmenu {{ is_open(['admin.features.index']) }}">
                    <a href="#!" class="nav-link"><span class="nav-icon"><i class="ti ti-award"></i></span><span
                            class="nav-text">{{
                            trans('dashboard/features.features') }}</span><span class="nav-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="nav-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ is_active('admin.features.index') }}"
                                href="{{route('admin.features.index')}}">{{
                                trans('dashboard/features.features')
                                }}</a>
                        </li>
                    </ul>
                </li>
                <!-- End Client -->
                @endOwnerOnly
            </ul>
        </div>
    </div>
</aside>
<!-- { navigation menu } end -->
