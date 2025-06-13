<?php

namespace App\Http\Requests\Office;

use Illuminate\Foundation\Http\FormRequest;

class PenaltyCreateRequest extends FormRequest
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
            // 'start' => ['required', 'numeric'],
            'start.*' => ['required', 'numeric'],
            // 'end' => ['required', 'numeric'],
            'end.*' => ['nullable'],
            // 'nominal' => ['required', 'numeric'],
            'nominal.*' => ['required', 'numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'start.*' => 'Menit awal',
            'end.*' => 'Menit akhir',
            'nominal.*' => 'Nominal',
        ];
    }
}
