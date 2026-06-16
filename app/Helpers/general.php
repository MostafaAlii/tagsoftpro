<?php
use Illuminate\Support\Facades\Route;
if (!function_exists('admin_guard')) {
    function admin_guard() {
        return auth('admin');
    }
}

if (!function_exists('company_guard')) {
    function company_guard() {
        return auth('company');
    }
}

if (!function_exists('client_guard')) {
    function client_guard() {
        return auth('client');
    }
}

if (!function_exists('employee_guard')) {
    function employee_guard()
    {
        return auth('employee');
    }
}

if (!function_exists('check_guard')) {
    function check_guard()
    {
        $guards = ['admin', 'company', 'client'];
        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                return $guard;
            }
        }
        return null;
    }
}


if (!function_exists('get_user_data')) {
    function get_user_data() {
        $guards = ['admin', 'company', 'client'];
        foreach ($guards as $guard) {
            if (auth($guard)->check())
                return auth($guard)->user();
        }
        return null;
    }
}

if (!function_exists('guard_dashboard_route')) {
    function guard_dashboard_route()
    {
        return match (check_guard()) {
            'admin' => route('admin.dashboard'),
            'company' => route('company.dashboard'),
            'employee' => route('employee.dashboard'),
            default => url('/'),
        };
    }
}
if (!function_exists('is_active')) {
    /**
     * Check if current route is active
     *
     * @param string|array $routes
     * @param string $output
     * @return string
     */
    function is_active($routes, $output = "active")
    {
        if (is_array($routes)) {
            return in_array(Route::currentRouteName(), $routes) ? $output : '';
        }
        return Route::currentRouteName() === $routes ? $output : '';
    }
}

if (!function_exists('is_open')) {
    /**
     * Check if dropdown should be open
     *
     * @param array $routes
     * @param string $output
     * @return string
     */
    function is_open($routes, $output = "nav-provoke")
    {
        return in_array(Route::currentRouteName(), $routes) ? $output : '';
    }
}