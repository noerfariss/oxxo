<?php

namespace App\Http\Controllers\Customer;

use App\Class\MemberClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LoginRequest;
use App\Http\Requests\Customer\RegisterRequest;
use App\Http\Requests\Customer\ValidationDataRequest;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('member.dashboard');
        }

        return view('customer.auth.login');
    }

    public function login(LoginRequest $request)
    {
        if (Auth::guard('member')->attempt($request->only(['whatsapp', 'password']))) {
            return redirect()->route('member.dashboard');
        }

        return redirect()->back()->with('message', '<div class="alert alert-danger">Whatsapp atau password Anda salah</div>');
    }

    public function register()
    {
        return view('customer.auth.register');
    }

    public function registerProcess(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $member = Member::create([
                'name' => $request->name,
                'numberid' => MemberClass::generateNumber(),
                'phone' => $request->whatsapp,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
            ]);
            DB::commit();

            // $memberClass->sendOtp($member);

            Auth::guard('member')->loginUsingId($member->id);

            return response()->json([
                'url' => route('member.dashboard'),
                'message' => 'Registrasi berhasil'
            ]);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());

            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan, cobalah kembali'
            ], 500);
        }
    }

    // public function verification($uuid)
    // {
    //     $member = Member::where('uuid', $uuid)->whereNull('verification')->first();

    //     if (! $member) {
    //         return abort(404);
    //     }

    //     return view('customer.auth.verification', compact('member'));
    // }

    // public function verificationProcess(Request $request, $uuid)
    // {
    //     $member = Member::where('uuid', $uuid)->whereNull('verification')->first();

    //     if (! $member) {
    //         return abort(404);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'otp' => ['required', 'digits:6'],
    //     ], attributes: [
    //         'otp' => 'Kode OTP'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validasi gagal.',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     if ($member->otp != $request->otp) {
    //         return response()->json([
    //             'message' => 'Kode OTP tidak sesuai',
    //             'errors' => ''
    //         ], 500);
    //     }

    //     $verificationExpired = Carbon::parse($member->verification_expired);
    //     if ($verificationExpired->lessThan(Carbon::now())) {
    //         return response()->json([
    //             'message' => 'Kode OTP kedaluwarsa, <b><a id="btn-resend" href="' . route('member.register.verification.resend', ['uuid' => $member->uuid]) . '">kirim kode</a></b> lagi',
    //             'errors' => ''
    //         ], 500);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $member->update([
    //             'verification' => Carbon::now()
    //         ]);
    //         DB::commit();

    //         return response()->json([
    //             'url' => route('member.register.data', ['uuid' => $member->uuid]),
    //             'message' => 'Verifikasi berhasil dilakukan'
    //         ]);
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         Log::warning($th->getMessage());

    //         return response()->json([
    //             'message' => 'Terjadi kesalahan, cobalah kembali'
    //         ], 500);
    //     }
    // }

    // public function resendVerfication($uuid)
    // {
    //     $member = Member::where('uuid', $uuid)->whereNull('verification')->first();

    //     if (! $member) {
    //         return abort(404);
    //     }

    //     try {
    //         $memberClass = new MemberClass();
    //         $member->update([
    //             'otp' => $memberClass->otp(),
    //             'verification_expired' => Carbon::now()->addMinutes(3),
    //         ]);

    //         $memberClass->sendOtp($member);

    //         return response()->json([
    //             'messsage' => 'Kode otp berhasil dikirim ulang',
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'messsage' => 'Kode otp gagal dikirim',
    //         ], 500);
    //     }
    // }

    // public function data($uuid)
    // {
    //     $member = Member::where('uuid', $uuid)->whereNotNull('verification')->first();

    //     if (! $member) {
    //         return abort(404);
    //     }

    //     return view('customer.auth.data', compact('member'));
    // }

    // public function validationData(ValidationDataRequest $request, $uuid)
    // {
    //     $member = Member::where('uuid', $uuid)->whereNotNull('otp')->whereNotNull('verification')->first();
    //     if (! $member) {
    //         return abort(404);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $member->update($request->only(['name', 'gender', 'address', 'district_id']));
    //         DB::commit();

    //         Auth::guard('member')->loginUsingId($member->id);

    //         return redirect()->route('member.dashboard')->with('message', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
    //     } catch (\Throwable $th) {
    //         Log::warning($th->getMessage());

    //         DB::rollBack();
    //         return redirect()->back()->with('message', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
    //     }
    // }
}
