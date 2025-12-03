<?php

namespace App\Controllers;

use SwiftPHP\Auth\Auth;
use SwiftPHP\Core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'user' => Auth::user(),
            'role' => Auth::role(),
            'isAdmin' => Auth::isAdmin()
        ]);
    }

    public function admin()
    {
        return view('dashboard.admin', [
            'user' => Auth::user()
        ]);
    }
}
