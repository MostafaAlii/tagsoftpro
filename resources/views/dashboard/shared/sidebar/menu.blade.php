<ul class="app-navbar">

    @include('dashboard.shared.sidebar.item', [
        'title' => trans('dashboard/header.main_dashboard'),
        'icon' => 'ti-layout-2',
        'route' => guard_dashboard_route(),
        'active' => is_active('admin.dashboard'),
    ])

    @include('dashboard.shared.sidebar.submenu', [
        'title' => trans('dashboard/sidebar.admin_main_settings_sidebar_title'),
        'icon' => 'ti-layout-2',
        'open' => is_open(['admin.mainSettings.index']),
        'items' => [
            [
                'title' => trans('dashboard/sidebar.main_settings_sidebar_title'),
                'route' => route('admin.mainSettings.index'),
                'active' => is_active('admin.mainSettings.index'),
            ],
        ],
    ])

    {{-- Menus --}}
    @include('dashboard.shared.sidebar.submenu', [
        'title' => trans('dashboard/menus.menus'),
        'icon' => 'ti-menu-2',
        'open' => is_open(['admin.menus.index']),
        'items' => [
            [
                'title' => trans('dashboard/menus.menus'),
                'route' => route('admin.menus.index'),
                'active' => is_active('admin.menus.index'),
            ],
        ],
    ])

    {{-- Permission Groups Sidebar --}}
    @include('dashboard.shared.sidebar.submenu', [
        'title' => trans('dashboard/sidebar.permission_roles'),
        'icon' => 'ti-lock',
        'open' => is_open(['admin.permission_groups.index']),
        'items' => [
            [
                'title' => trans('dashboard/permission_groups.permission_groups'),
                'route' => route('admin.permission_groups.index'),
                'active' => is_active('admin.permission_groups.index'),
            ],
        ],
    ])

    @include('dashboard.shared.sidebar.submenu', [
        'title' => trans('dashboard/employees.employees'),
        'icon' => 'ti-users',
        'open' => is_open(['admin.employees.index']),
        'items' => [
            [
                'title' => trans('dashboard/employees.employees'),
                'route' => route('admin.employees.index'),
                'active' => is_active('admin.employees.index'),
            ],
        ],
    ])

    @ownerOnly
        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/themes.themes'),
            'icon' => 'ti-palette',
            'open' => is_open(['admin.themes.index']),
            'items' => [
                    [
                    'title' => trans('dashboard/themes.themes'),
                    'route' => route('admin.themes.index'),
                    'active' => is_active('admin.themes.index'),
                    ],
                ],
        ])
        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/departments.departments'),
            'icon' => 'ti-building',
            'open' => is_open(['admin.departments.index']),
            'items' => [
                [
                    'title' => trans('dashboard/departments.departments'),
                    'route' => route('admin.departments.index'),
                    'active' => is_active('admin.departments.index'),
                ],
            ],
        ])

        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/sidebar.admin_client_sidebar_title'),
            'icon' => 'ti-award',
            'open' => is_open(['admin.clients.index']),
            'items' => [
                [
                    'title' => trans('dashboard/sidebar.client_sidebar_title'),
                    'route' => route('admin.clients.index'),
                    'active' => is_active('admin.clients.index'),
                ],
            ],
        ])

        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/project_types.project_types'),
            'icon' => 'ti-folder',
            'open' => is_open(['admin.projectTypes.index']),
            'items' => [
                [
                    'title' => trans('dashboard/project_types.project_types'),
                    'route' => route('admin.projectTypes.index'),
                    'active' => is_active('admin.projectTypes.index'),
                ],
            ],
        ])

        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/modules.modules'),
            'icon' => 'ti-gears',
            'open' => is_open(['admin.modules.index']),
            'items' => [
                [
                    'title' => trans('dashboard/modules.modules'),
                    'route' => route('admin.modules.index'),
                    'active' => is_active('admin.modules.index'),
                ],
            ],
        ])

        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/projects.projects'),
            'icon' => 'ti-folder-plus',
            'open' => is_open(['admin.projects.index']),
            'items' => [
                [
                    'title' => trans('dashboard/projects.projects'),
                    'route' => route('admin.projects.index'),
                    'active' => is_active('admin.projects.index'),
                ],
            ],
        ])

        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('dashboard/features.features'),
            'icon' => 'ti-award',
            'open' => is_open(['admin.features.index']),
            'items' => [
                [
                    'title' => trans('dashboard/features.features'),
                    'route' => route('admin.features.index'),
                    'active' => is_active('admin.features.index'),
                ],
            ],
        ])

        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('vendor::vendors.vendors'),
            'icon' => 'ti-award',
            'open' => is_open(['admin.vendors.index']),
            'items' => [
                [
                    'title' => trans('vendor::vendors.vendors'),
                    'route' => route('admin.vendors.index'),
                    'active' => is_active('admin.vendors.index'),
                ],
            ],
        ])
        @include('dashboard.shared.sidebar.submenu', [
            'title' => trans('zone::zones.zones'),
            'icon' => 'ti-award',
            'open' => is_open(['admin.zones.index']),
            'items' => [
                [
                    'title' => trans('zone::zones.zones'),
                    'route' => route('admin.zones.index'),
                    'active' => is_active('admin.zones.index'),
                ],
            ],
        ])
    @endOwnerOnly
</ul>
