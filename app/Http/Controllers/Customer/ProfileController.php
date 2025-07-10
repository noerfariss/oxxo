<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PasswordUpdateRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('customer.profile.index');
    }

    public function profileProcess(Request $request)
    {
        DB::beginTransaction();
        try {
            Member::find(Auth::guard('member')->id())->update($request->only(['name', 'email', 'whatsapp', 'gender', 'address', 'district_id']));
            DB::commit();
            return redirect()->back()->with('message', '<div class="alert alert-success">Profil berhasil diperbaruhi</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return redirect()->back()->with('message', '<div class="alert alert-danger">Terjadi kesalahan, silahkan coba lagi</div>');
        }
    }

    public function password()
    {
        return view('customer.profile.password');
    }

    public function passwordProcess(PasswordUpdateRequest $request)
    {
        DB::beginTransaction();
        try {

            if (Hash::check($request->password_lama, Auth::guard('member')->user()->password)) {
                Member::find(Auth::guard('member')->id())->update([
                    'password' => Hash::make($request->password),
                ]);

                DB::commit();

                return redirect()->back()->with('message', '<div class="alert alert-success">Password berhasil diperbaruhi</div>');
            } else {
                return redirect()->back()->with('message', '<div class="alert alert-danger">Password lama salah!</div>');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return redirect()->back()->with('message', '<div class="alert alert-danger">Terjadi kesalahan, silahkan coba lagi</div>');
        }
    }
}
