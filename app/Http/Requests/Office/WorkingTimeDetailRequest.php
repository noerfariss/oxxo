<?php

namespace App\Http\Requests\Office;

use Illuminate\Foundation\Http\FormRequest;

class WorkingTimeDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day' => ['required'],
            'checkin' => ['required'],
            'checkout' => ['required'],
            'day.*' => ['required'],
            'checkin.*' => ['required'],
            'checkout.*' => ['required'],
            'early' => ['required', 'numeric'],
            'description' => ['required'],
            'tolerance' => ['required', 'numeric'],
            'isdefault' => ['nullable']
        ];
    }

    public function attributes()
    {
        return [
            'description' => 'Keterangan',
            'early' => 'Waktu lebih awal',
            'tolerance' => 'Waktu toleransi',
            'day' => 'Hari',
            'checkin' => 'Jam Masuk',
            'checkout' => 'Jam Pulang',
        ];
    }
}
