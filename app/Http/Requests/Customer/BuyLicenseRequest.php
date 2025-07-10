<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class BuyLicenseRequest extends FormRequest
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
            'is_demo' => ['required'],
            'payment_channel' => ['required_unless:is_demo,0']
        ];
    }

    public function attributes()
    {
        return [
            'is_demo' => 'Jenis lisensi',
            'payment_channel' => 'Metode Pembayaran'
        ];
    }

    public function messages()
    {
        return [
            'payment_channel.required_unless' => 'Pembayaran wajib diisi'
        ];
    }
}
