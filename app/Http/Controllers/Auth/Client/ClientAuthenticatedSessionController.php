<?php

namespace App\Http\Controllers\Auth\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Mail\Client\ClientResetPassword;
use Illuminate\Support\Facades\{DB, Mail};
use App\Enums\Client\ClientStatus;

class ClientAuthenticatedSessionController extends Controller
{
    protected $redirectRouteName = 'client.dashboard';
    protected $loginViewPath = 'dashboard.client.auth.login';
    protected $forgotPasswordViewPath = 'dashboard.client.auth.forgot-password';
    protected $LogoutRidirectRouteName = 'client.login';

    public function create()
    {
        return view($this->loginViewPath, ['title' => trans('dashboard/auth.client_auth_form_title')]);
    }

    public function store(ClientLoginRequest $request) {
        $credentials = $request->only('email', 'password');
        if (client_guard()->attempt($credentials)) {
            $client = client_guard()->user();
            switch ($client->status) {
                case ClientStatus::ACTIVE:
                    return redirect()->route($this->redirectRouteName)->with('success', trans('dashboard/auth.success_login_msg'));
                case ClientStatus::IN_ACTIVE:
                    client_guard()->logout();
                    return redirect()->back()->with('warning', trans('dashboard/auth.not_active_account_msg'));
                case ClientStatus::BLOCKED:
                    client_guard()->logout();
                    return redirect()->back()->with('error', trans('dashboard/auth.blocked_account_msg'));
                default:
                    client_guard()->logout();
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
        client_guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($this->LogoutRidirectRouteName);
    }
}
