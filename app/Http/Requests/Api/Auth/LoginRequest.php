<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'nik' => ['required', 'numeric'],
            'password' => ['required', 'min:6'],
            'devices' => ['nullable'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'address' => ['nullable'],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'status' => true
        ]);
    }
}
