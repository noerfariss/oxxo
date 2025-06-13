<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function index()
    {
        return view('member.checklog.report');
    }
}
