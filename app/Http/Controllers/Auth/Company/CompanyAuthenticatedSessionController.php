<?php

namespace App\Http\Controllers\Auth\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CompanyLoginRequest;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Mail\Admin\AdminResetPassword;
use Illuminate\Support\Facades\{DB, Mail};
use App\Enums\Company\CompanyStatus;

class CompanyAuthenticatedSessionController extends Controller
{
    protected $redirectRouteName = 'company.dashboard';
    protected $loginViewPath = 'dashboard.company.auth.login';
    protected $forgotPasswordViewPath = 'dashboard.company.auth.forgot-password';
    protected $LogoutRidirectRouteName = 'company.login';

    public function create()
    {
        return view($this->loginViewPath, ['title' => trans('dashboard/auth.company_auth_form_title')]);
    }

    public function store(CompanyLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (company_guard()->attempt($credentials)) {
            $company = company_guard()->user();
            switch ($company->status) {
                case CompanyStatus::ACTIVE:
                    return redirect()->route($this->redirectRouteName)->with('success', trans('dashboard/auth.success_login_msg'));
                case CompanyStatus::IN_ACTIVE:
                    company_guard()->logout();
                    return redirect()->back()->with('warning', trans('dashboard/auth.not_active_account_msg'));
                case CompanyStatus::BLOCKED:
                    company_guard()->logout();
                    return redirect()->back()->with('error', trans('dashboard/auth.blocked_account_msg'));
                default:
                    company_guard()->logout();
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
        company_guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($this->LogoutRidirectRouteName);
    }
}