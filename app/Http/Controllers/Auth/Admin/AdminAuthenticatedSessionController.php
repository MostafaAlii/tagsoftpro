<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Mail\Admin\AdminResetPassword;
use Illuminate\Support\Facades\{DB, Mail};
use App\Enums\Admin\AdminStatus;

class AdminAuthenticatedSessionController extends Controller
{
    protected $redirectRouteName = 'admin.dashboard';
    protected $loginViewPath = 'dashboard.admin.auth.login';
    protected $forgotPasswordViewPath = 'dashboard.admin.auth.forgot-password';
    protected $LogoutRidirectRouteName = 'admin.login';

    public function create()
    {
        return view($this->loginViewPath, ['title' => trans('dashboard/auth.admin_auth_form_title')]);
    }

    public function store(AdminLoginRequest $request) {
        $credentials = $request->only('email', 'password');
        if (admin_guard()->attempt($credentials)) {
            $admin = admin_guard()->user();
            switch ($admin->status) {
                case AdminStatus::ACTIVE:
                    return redirect()->route($this->redirectRouteName)->with('success', trans('dashboard/auth.success_login_msg'));
                case AdminStatus::IN_ACTIVE:
                    admin_guard()->logout();
                    return redirect()->back()->with('warning', trans('dashboard/auth.not_active_account_msg'));
                case AdminStatus::BLOCKED:
                    admin_guard()->logout();
                    return redirect()->back()->with('error', trans('dashboard/auth.blocked_account_msg'));
                default:
                    admin_guard()->logout();
                    return redirect()->back()->with('error', trans('dashboard/auth.unknown_status_msg'));
            }
        }
        return redirect()->back()->with('error', trans('dashboard/auth.login_credential_failure'));
    }

    public function forgot_password()
    {
        return view($this->forgotPasswordViewPath);
    }

    public function destroy(Request $request)
    {
        admin_guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($this->LogoutRidirectRouteName);
    }
}
