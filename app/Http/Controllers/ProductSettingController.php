<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductSettingController extends Controller
{
    public function index()
    {
        return view('member.productsetting.index');
    }
}
