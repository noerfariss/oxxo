<?php

namespace App\Http\Controllers\Api;

use App\Class\LogClass;
use App\Class\MemberClass;
use App\Class\ResponseClass;
use App\Class\TokenClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\PasswordUpdateRequest;
use App\Http\Requests\Api\UpdatePhotoProfilRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Models\Member;
use App\Models\MemberDevice;
use App\Trait\ApiPhoto;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiPhoto;

    public function login(LoginRequest $request)
    {
        $this->ensureIsNotRateLimited($request);

        if (!$auth = Auth::guard('api')->attempt($request->only('nik', 'password', 'status'))) {
            RateLimiter::hit($this->throttleKey($request));

            return ResponseClass::error('Nik atau password Anda salah!', statusCode: 401);
        }

        RateLimiter::clear($this->throttleKey($request));

        // Hasilkan access_token
        $accessToken = Auth::guard('api')->tokenById(Auth::guard('api')->id());

        // Hasilkan refresh_token (string acak untuk keamanan)
        $refreshToken = Str::random(80);

        // simpan toke by DB
        Member::where('id', Auth::guard('api')->id())->update([
            'refresh_token' => $refreshToken,
            'token' => $accessToken,
        ]);

        MemberDevice::create([
            'member_id' => Auth::guard('api')->id(),
            'description' => 'Login',
            'devices' => $request->devices,
            'ip' => request()->ip(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ]);

        LogClass::set('Login', true);

        return ResponseClass::success('Login berhasil', TokenClass::respondWithToken($accessToken, $refreshToken));
    }

    public function user()
    {
        $data = MemberClass::updatecache();
        return ResponseClass::success(data: $data);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->header('refreshtoken');
        $refreshToken = str_replace('Bearer ', '', $refreshToken);

        $user = Member::where('refresh_token', $refreshToken)->first();

        if (!$user) {
            return ResponseClass::error('Anda telah logout', statusCode: 401);
        }

        $newToken = Auth::tokenById($user->id);

        LogClass::set('Refresh token', true);

        return ResponseClass::success(data: TokenClass::refresh($newToken));
    }

    public function logout(Request $request)
    {
        $user = request()->user();
        $user->update([
            'refresh_token' => null,
            'token' => null,
        ]);

        MemberDevice::create([
            'member_id' => Auth::guard('api')->id(),
            'description' => 'Logout',
            'devices' => $request->devices,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ]);

        LogClass::set('Logout', true);

        Auth::guard('api')->logout(true);
        return ResponseClass::success('Logout Sukses');
    }

    public function password(PasswordUpdateRequest $request)
    {
        try {
            $user = request()->user();
            if (Hash::check($request->password_old, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                LogClass::set('Change password', true);

                return ResponseClass::success('Password berhasil diperbarui');
            } else {
                return ResponseClass::error('Password lama salah');
            }
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function updateProfile(UpdateUserRequest $request)
    {
        $user = request()->user();

        DB::beginTransaction();
        try {
            $user->update($request->only(['name', 'email', 'phone', 'gender']));
            DB::commit();

            MemberClass::updatecache(true);

            LogClass::set('Update profile', true);

            return ResponseClass::success('Profil berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function updatePhotoProfile(UpdatePhotoProfilRequest $request)
    {
        $user = request()->user();

        DB::beginTransaction();
        try {
            $user->update([
                'photo' => $this->savePhoto($request->photo, 'member'),
            ]);
            DB::commit();

            MemberClass::updatecache(true);

            LogClass::set('Update photo profile', true);

            return ResponseClass::success('Foto profil berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }


    public function ensureIsNotRateLimited($request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'phone' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 10),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey($request): string
    {
        return Str::transliterate(Str::lower($request->input('phone')) . '|' . $request->ip());
    }
}
