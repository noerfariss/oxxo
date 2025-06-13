<?php

namespace App\Http\Controllers;

use App\Class\ResponseClass;
use App\Imports\EmployeeImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class MemberTemplateController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:MEMBER_CREATE', only: ['template', 'import']),
        ];
    }

    public function template()
    {
        return response()->download(public_path('/files/EMPLOYEE_TEMPLATE.xlsx'));
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:xls,xlsx'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        DB::beginTransaction();
        try {
            // Ambil heading row
            $headingRow = (new HeadingRowImport)->toArray($request->file('file'));
            $headers = array_map('strtolower', $headingRow[0][0]); // header di row pertama
            $expectedHeaders = ['nik', 'name', 'email', 'phone', 'gender', 'password', 'kantor', 'divisi', 'jabatan'];

            // Cek apakah header sesuai template
            if (array_diff($expectedHeaders, $headers)) {
                return redirect()->back()->with('pesan', '<div class="alert alert-danger"><b>Import Gagal!</b> Format file tidak sesuai template. Harap gunakan template yang benar</div>');
            }

            // cek apakah ada datanya?
            $rows = Excel::toCollection(new EmployeeImport, $request->file('file'));
            if (count($rows->toArray()[0]) < 1) {
                return redirect()->back()->with('pesan', '<div class="alert alert-danger"><b>Import Gagal!</b> File tidak mengandung data</div>');
            }

            Excel::import(new EmployeeImport, $request->file('file'));

            DB::commit();

            return redirect()->back()->with('pesan', '<div class="alert alert-success"><b>Import Berhasil!</b></div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger"><b>Import Gagal!</b> Terjadi kesalahan server</div>');
        }
    }
}
