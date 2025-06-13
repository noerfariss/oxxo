<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReceiptAndCreditEnum;
use App\Events\CreditTenorEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreditCreateRequest;
use App\Http\Resources\Api\CreditResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    public function list()
    {
        try {
            $member_id = request()->user()->id;
            $credit = Credit::where('member_id', $member_id)
                ->select('uuid', 'nominal', 'tenor', 'description', 'admin_approved', 'manager_approved', 'is_accepted', 'created_at')
                ->where('status', true)
                ->orderBy('id', 'desc')
                ->get();

            if ($credit->count() > 0) {
                return CreditResource::collection($credit);
            } else {
                return new SuccessResource(['message' => 'Belum ada pinjaman']);
            }
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return new ErrorResource();
        }
    }

    public function total()
    {
        try {
            $member_id = request()->user()->id;
            $credit = DB::table('credits as a')
                ->select(DB::raw('SUM(b.nominal) as total'))
                ->join('credit_tenors as b', 'b.credit_id', '=', 'a.id')
                ->where('a.member_id', $member_id)
                ->where('a.status', true)
                ->where('a.admin_approved', ReceiptAndCreditEnum::APPROVED)
                ->where('a.manager_approved', ReceiptAndCreditEnum::APPROVED)
                ->where('a.is_accepted', ReceiptAndCreditEnum::APPROVED)
                ->where('b.status', false)
                ->first();

            return new SuccessResource(['data' => $credit->total]);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return new ErrorResource();
        }
    }

    public function detail($uuid)
    {
        try {
            $member_id = request()->user()->id;
            $credit = Credit::query()
                ->where('member_id', $member_id)
                ->where('uuid', $uuid)
                ->first();

            if ($credit !== NULL) {
                return new CreditResource($credit);
            } else {
                return new SuccessResource(['message' => 'Belum ada pinjaman']);
            }
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return new ErrorResource();
        }
    }

    public function store(CreditCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $credit = Credit::create($request->only(['member_id', 'members', 'tenor', 'nominal', 'description']));

            CreditTenorEvent::dispatch($credit);

            DB::commit();
            return new SuccessResource(['message' => 'Pengajuan pinjaman berhasil diajukan']);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return new ErrorResource();
        }
    }
}
