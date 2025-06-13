<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StateController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:STATE_CREATE', only: ['create', 'store']),
            new Middleware('permission:STATE_READ', only: ['index']),
            new Middleware('permission:STATE_EDIT', only: ['edit', 'update']),
            new Middleware('permission:STATE_DELETE', only: ['delete']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        //
    }
}
