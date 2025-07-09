<?php

namespace App\Http\Controllers;

use App\Class\DepositClass;
use App\Class\LogClass;
use App\Class\ResponseClass;
use App\Enums\DepositEnum;
use App\Models\Deposit;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Member $member)
    {
        if (!$member->is_member) {
            abort(403);
        }

        $saldo = DepositClass::saldo($member);

        return view('member.deposit.index', compact('member', 'saldo'));
    }

    public function ajax(Request $request)
    {
        $dates = pecahTanggal($request->dates);
        $member = $request->member;

        $data = Deposit::query()
            ->when($member, fn($e) => $e->where('member_id', $member))
            ->where(function ($e) use ($dates) {
                $e->whereBetween('dates', [$dates[0], $dates[1]]);
            });

        return DataTables::eloquent($data)
            ->make(true);
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
        $validator = Validator::make($request->all(), [
            'dates' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1000000'],
            'note' => ['nullable', 'min:3']
        ], attributes: [
            'amount' => 'Total',
            'note' => 'Catatan'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = Deposit::create([
                'dates' => $request->dates,
                'member_id' => $request->member_id,
                'type' => DepositEnum::IN->value,
                'amount' => $request->amount,
                'note' => $request->note
            ]);

            DB::commit();

            return ResponseClass::success();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit $deposit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deposit $deposit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit $deposit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit)
    {
        //
    }
}
