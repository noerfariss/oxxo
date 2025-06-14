<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CashierController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:CASHIER_CREATE', only: ['create', 'store']),
            new Middleware('permission:CASHIER_READ', only: ['index']),
            new Middleware('permission:CASHIER_EDIT', only: ['edit', 'update']),
            new Middleware('permission:CASHIER_DELETE', only: ['delete']),
        ];
    }

    public function index()
    {
        return view('member.cashier.index');
    }
}
