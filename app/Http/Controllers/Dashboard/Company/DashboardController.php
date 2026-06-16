<?php

namespace App\Http\Controllers\Dashboard\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard.company.dashboard', ['title' => trans('dashboard/header.main_dashboard')]);
    }
}