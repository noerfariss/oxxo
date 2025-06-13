<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Events\LogEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('auth.index');
        }

        return view('auth.login');
    }

    // Auth administrator
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only(['email', 'password', 'status']))) {

                // Log
                LogClass::set('Login');

                return redirect()->route('auth.index');
            } else {

                // Log
                LogClass::set($request->email . ' gagal login');

                return redirect()->route('login')->with('pesan', '<div class="alert alert-danger">Email atau password salah!</div>');
            }


            return redirect()->route('login')->with('pesan', '<div class="alert alert-success">Registrasi berhasil, silahkan cek email Anda untuk verifikasi</div>');
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());

            return redirect()->route('login')->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    public function logout()
    {
        // Log
        LogClass::set('Logout');

        Auth::logout();

        return redirect()->route('login');
    }
}
