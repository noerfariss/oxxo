<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReceiptCreateRequest;
use App\Http\Resources\Api\ReceiptResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Member;
use App\Models\Receipt;
use App\Models\Setting;
use App\Trait\GlobalTrait;
use App\Trait\ReceiptTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    use ReceiptTrait, GlobalTrait;

    public $max;

    public function __construct(Setting $setting)
    {
        $settings = json_decode($setting->first()->setting);
        $this->max = $settings->receipt_max;
    }

    public function limit()
    {
        $member_id = request()->user()->id;
        $data = $this->checkReceipt($member_id);
        $max = $this->max;

        // cek jika ada pengajuan di bulan ini
        if (count($data) > 0) {
            $total = collect($data)->sum('nominal');
            $limit = $max - $total;

            if ($total < $max) {
                return new SuccessResource([
                    'message' => 'Kasbon tersedia',
                    'data' => [
                        'limit_human' => $this->RupiahFormat($limit),
                        'limit' => $limit
                    ]
                ]);
            } else {
                return new ErrorResource([
                    'message' => 'Kasbon sudah batas maksimum di bulan ini',
                    'data' => [
                        'limit_human' => $this->RupiahFormat($limit),
                        'limit' => $limit
                    ]
                ]);
            }
        } else {
            return new SuccessResource([
                'message' => 'Kasbon tersedia',
                'data' => [
                    'limit_human' => $this->RupiahFormat($max),
                    'limit' => $max
                ]
            ]);
        }
    }

    public function store(ReceiptCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            // Cek sudah ada kasbon apa belum
            $member_id = request()->user()->id;
            $data = $this->checkReceipt($member_id);
            if (count($data) > 0) {
                $max = $this->max;
                $total = collect($data)->sum('nominal');
                $limit = $max - $total;
                $nominal = $request->nominal;

                // Jika di DB totalnya sudah lebih dari samadengan MAX
                if ($total >= $max) {
                    return new ErrorResource(['message' => 'Kasbon sudah batas maksimum di bulan ini']);
                }

                // Jika di DB total dibanding dengan nominal yang diinputkan tidak boleh lebih dari batas MAX
                else if ($nominal > $limit) {
                    return new ErrorResource([
                        'message' => 'Nominal yang Anda masukkan tidak boleh lebih dari sisa limit',
                        'data' => [
                            'limit_human' => $this->RupiahFormat($limit),
                            'limit' => $limit
                        ],
                    ]);
                }
            }

            Receipt::create($request->only(['member_id', 'members', 'dates', 'nominal']));

            DB::commit();
            return new SuccessResource(['message' => 'Kas bon berhasil diajukan']);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return new ErrorResource();
        }
    }

    public function list()
    {
        try {
            $member_id = request()->user()->id;
            $data = Receipt::query()
                ->where('member_id', $member_id)
                ->where('status', true)
                ->get();

            return ReceiptResource::collection($data);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());

            return new ErrorResource();
        }
    }
}
