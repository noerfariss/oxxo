<?php

namespace App\Http\Controllers\Customer;

use App\Class\DepositClass;
use App\Class\LicenseClass;
use App\Class\PaymentClass;
use App\Enums\LicenseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\BuyLicenseRequest;
use App\Models\Member;
use App\Models\Member\License;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class LicenseController extends Controller
{
    public function index()
    {
        $member = Member::find(Auth::guard('member')->id());
        $data = DepositClass::saldo($member);

        return view('customer.license.index', compact('data'));
    }

    public function show($license)
    {
        $license = License::with('payment')->where('uuid', $license)->first();
        if (!$license) {
            abort(404);
        }

        // dd($license->toArray());

        return view('customer.license.show', compact('license'));
    }

    public function create()
    {
        return view('customer.license.create');
    }

    public function store(BuyLicenseRequest $request)
    {
        $isDemo = (int) $request->is_demo;
        $payment = new PaymentClass();

        DB::beginTransaction();
        try {
            $licenseKey = LicenseClass::generate(Auth::guard('member')->user()->email);

            $license = License::create([
                'uuid' => Uuid::uuid7(),
                'member_id' => Auth::guard('member')->id(),
                'license' => $licenseKey,
                'is_demo' => $isDemo,
                'total' => $request->total,
                'status' => $isDemo ? false : true // jika jenis lisensi DEMO otomatis aktif
            ]);

            // jika jenis lisensi selain DEMO maka buatkan pembayaran
            if ($isDemo) {

                // buat invoice sementara
                $inputPayment = Payment::create([
                    'member_id' => Auth::guard('member')->id(),
                    'license_id' => $license->id,
                    'merchant_ref' => $payment->generateCode(),
                ]);

                $merchantRef = $inputPayment->merchant_ref;
                $amount = $request->total;
                $signature = $payment->signature($merchantRef, $amount);

                $payload = [
                    'method'         => $request->payment_channel,
                    'merchant_ref'   => $merchantRef,
                    'amount'         => $amount,
                    'customer_name'  => Auth::guard('member')->user()->name,
                    'customer_email' => Auth::guard('member')->user()->email,
                    'customer_phone' => Auth::guard('member')->user()->whatsapp,
                    'order_items'    => [
                        [
                            'name' => $license->license,
                            'quantity' => 1,
                            'price' => $license->total,
                            'type' => LicenseEnum::from((int) $license->is_demo)->label()
                        ]
                    ],
                    'signature'      => $signature,
                ];

                $data = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $payment->publicKey,
                    'Accept' => 'application/json'
                ])
                    ->post(
                        $payment->paymentUrl . '/transaction/create',
                        $payload
                    )
                    ->object();

                if (!$data->success) {
                    return response()->json([
                        'message' => $data->message
                    ], 401);
                }

                // simpan pada table paymenet
                $tripay = $data->data;
                $getPayment = Payment::find($inputPayment->id);

                $update = $getPayment->update([
                    'reference' => $tripay->reference,
                    'member_text' => [
                        'name' => $tripay->customer_name,
                        'email' => $tripay->customer_email,
                        'phone' => $tripay->customer_phone
                    ],
                    'order' => $tripay->order_items,
                    'amount' => $tripay->amount,
                    'fee_merchant' => $tripay->fee_merchant,
                    'fee_customer' => $tripay->fee_customer,
                    'total_fee' => $tripay->total_fee,
                    'amount_received' => $tripay->amount_received,
                    'payment_method' => $tripay->payment_method,
                    'payment_name' => $tripay->payment_name,
                    'payment_status' => $tripay->status,
                    'pay_code' => $tripay->pay_code,
                    'pay_url' => $tripay->pay_url,
                    'checkout_url' => $tripay->checkout_url,
                    'qr_string' => isset($tripay->qr_string) ? $tripay->qr_string : NULL,
                    'qr_url' => isset($tripay->qr_url) ? $tripay->qr_url : NULL,
                    'expired_time' => $tripay->expired_time,
                    'instructions' => $tripay->instructions
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => $isDemo ? 'Pembuatan lisensi berhasil, segera lakukan pembayaran' : 'Pembuatan lisensi berhasil',
                // 'data' => $isDemo ? $getPayment->toArray() : [],
                'redirect' => route('customer.license.show', ['license' => $license->uuid]),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
