<?php

namespace App\Http\Requests\Office;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkingTimeAddRequest extends FormRequest
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
            'description' => ['required'],
            'early' => ['required', 'numeric'],
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
            'isdefault' => 'Jam utama'
        ];
    }
}
