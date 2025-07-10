<?php

namespace App\Class;

use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PaymentClass
{
    public $paymentUrl;
    public $privatKey;
    public $publicKey;
    public $merchantCode;

    public function __construct()
    {
        $this->privatKey = env('APP_ENV') === 'local' ? env('TRIPAY_PRIVAT_SANBOX') : env('TRIPAY_PRIVAT_PRODUCTION');
        $this->publicKey = env('APP_ENV') === 'local' ? env('TRIPAY_PUBLIC_SANBOX') : env('TRIPAY_PUBLIC_PRODUCTION');
        $this->paymentUrl = env('APP_ENV') === 'local' ? env('TRIPAY_URL_SANBOX') : env('TRIPAY_URL_PRODUCTION');
        $this->merchantCode = env('APP_ENV') === 'local' ? 'T36398' : 'T37111';
    }

    public function signature($merchantRef = '', $amount = 0)
    {
        $privateKey   = $this->privatKey;
        $merchantCode = $this->merchantCode;

        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

        return $signature;
    }

    public function generateCode()
    {
        $userid = Auth::guard('member')->id();
        $now = Carbon::now();
        $year = $now->format('Y');
        $month = $now->format('m');
        $day = $now->format('d');

        // Kode Hari (S, SL, R, K, J, S, M)
        $dayCodes = ['Minggu' => 'M', 'Senin' => 'S', 'Selasa' => 'SL', 'Rabu' => 'R', 'Kamis' => 'K', 'Jumat' => 'J', 'Sabtu' => 'S'];
        $dayCode = $dayCodes[$now->translatedFormat('l')];

        $count = Payment::query()
            ->where('member_id', $userid)
            ->whereYear('created_at', $now->year)
            ->count() + 1;

        $sequence = str_pad($count, 4, '0', STR_PAD_LEFT); // 4 digit kode urut

        return "INV/{$dayCode}{$userid}{$year}{$day}{$month}/{$sequence}";
    }
}
