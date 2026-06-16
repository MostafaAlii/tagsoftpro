<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        
        return view('dashboard.admin.dashboard', ['title' => trans('dashboard/header.main_dashboard')]);
    }
}