<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function adminDashboard()
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect('/user/dashboard');
        }

        return view('dashboard.admin');
    }

    public function userDashboard()
    {
        if (!auth()->user()->hasRole('user')) {
            return redirect('/admin/dashboard');
        }

        return view('dashboard.user');
    }
}
