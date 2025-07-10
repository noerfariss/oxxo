<?php

namespace App\Http\Controllers;

use App\Class\PaymentClass;
use App\Models\Member\License;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function listChannel()
    {
        $payment = new PaymentClass();

        try {
            $data = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $payment->publicKey,
                ])
                ->get($payment->paymentUrl . '/merchant/payment-channel')
                ->object();

            return $data;
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $payment = new PaymentClass();

        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, $payment->privatKey);
        // dd($signature);

        if ($signature !== (string) $callbackSignature) {
            info('Callback => Invalid signature');
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ]);
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            info('Unrecognized callback event, no action was taken');
            return response()->json([
                'success' => false,
                'message' => 'Unrecognized callback event, no action was taken',
            ]);
        }

        $data = json_decode($json);

        if (JSON_ERROR_NONE !== json_last_error()) {
            info('Invalid data sent by tripay');

            return response()->json([
                'success' => false,
                'message' => 'Invalid data sent by tripay',
            ]);
        }

        $invoiceId = $data->merchant_ref;
        $tripayReference = $data->reference;
        $status = strtoupper((string) $data->status);

        if ($data->is_closed_payment === 1) {
            $invoice = Payment::where('merchant_ref', $invoiceId)
                ->where('reference', $tripayReference)
                ->where('payment_status', '=', 'UNPAID')
                ->first();

            // update lisens
            if ($invoice) {
                $license = License::find($invoice->license_id);
            }

            if (! $invoice) {
                info('Callback => No invoice found or already paid: ' . $invoiceId);
                return response()->json([
                    'success' => false,
                    'message' => 'No invoice found or already paid: ' . $invoiceId,
                ]);
            }

            switch ($status) {
                case 'PAID':
                    $invoice->update([
                        'payment_status' => 'PAID',
                        'payment_date' => Carbon::now()
                    ]);
                    $license->update(['status' => true]);
                    break;

                case 'EXPIRED':
                    $invoice->update(['payment_status' => 'EXPIRED']);
                    break;

                case 'FAILED':
                    $invoice->update(['payment_status' => 'FAILED']);
                    break;

                default:
                    info('Callback => Unrecognized payment status ' . $invoiceId);
                    return response()->json([
                        'success' => false,
                        'message' => 'Unrecognized payment status',
                    ]);
            }

            return response()->json(['success' => true]);
        }
    }
}
