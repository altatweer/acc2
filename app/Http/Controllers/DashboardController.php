<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard'); // هذا يفتح الملف اللي كتبته أنت الآن (dashboard.blade.php)
    }
}
