<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EmployeeImport implements ToModel, WithHeadingRow, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }

    public function model(array $row)
    {
        $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);
        $row = array_change_key_case($row, CASE_LOWER);

        Log::info('ROW:', $row);

        return new Member([
            'nik' => $row['nik'] ?: rand(111111, 999999),
            'phone' => $row['phone'],
            'email' => $row['email'],
            'name' => $row['name'],
            'gender' => $row['gender'],
            'password' => $row['password'] ? Hash::make($row['password']) : Hash::make('123456'),
            'office_id' => $row['kantor'],
            'division_id' => $row['divisi'],
            'position_id' => $row['jabatan'],
        ]);
    }
}
