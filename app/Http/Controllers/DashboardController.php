<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard.dashboard');
    }

    public function driver()
    {
        return view('driver.dashboard.dashboard');
    }
}
