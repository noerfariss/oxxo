<?php

namespace App\Class;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MemberClass
{
    public static function updatecache($clear = false)
    {
        if ($clear) {
            Cache::forget('member-' . Auth::guard('api')->id());
        }

        $data = Cache::remember('member-' . Auth::guard('api')->id(), 60, function () {
            return Member::query()
                ->with([
                    'office:id,name,address,latitude,longitude,radius,wfh,city_id',
                    'office.city:id,name,state_id',
                    'office.city.state:id,name,timezone',
                    'office.workingtime',
                    'office.workingtime.working_detail',
                    'division:id,name',
                    'position:id,name'
                ])
                ->select(
                    'id',
                    'uuid',
                    'nik',
                    'name',
                    'email',
                    'phone',
                    'gender',
                    'photo',
                    'status',
                    'office_id',
                    'division_id',
                    'position_id'
                )
                ->find(Auth::guard('api')->id());
        });

        return $data;
    }

    public static function generateNumber()
    {
        // Ambil tanggal hari ini dalam format Ymd
        $date = now()->format('Ymd');

        // Ambil jumlah member yang didaftarkan hari ini
        $count = Member::whereDate('created_at', now()->toDateString())->count() + 1;

        // Format nomor urut dengan padding nol
        $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

        // Gabungkan kode
        return "MBR{$date}-{$sequence}";
    }

    public function sendOtp(Member $member)
    {
        $pesan = "Haloo *" . $member->email . "*,\n\nTerima kasih sudah melakukan Registrasi. Segera aktivasi dengan memasukkan kode: *" . $member->otp . "* \n\n" . Str::random(10);

        try {

            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_KEY'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'

            ])->post(env('FONNTE_SENDER'), [
                'target' => $member->whatsapp,
                'countryCode' => '62',
                'preview' => false,
                'message' => $pesan,
            ]);
        } catch (\Throwable $th) {
            info('Fonnte error: ' . $th->getMessage());
        }
    }
}
