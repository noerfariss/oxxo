<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('customer.dashboard.home');
    }

    public function logout()
    {
        Auth::guard('member')->logout();

        return redirect()->route('member.login');
    }
}
