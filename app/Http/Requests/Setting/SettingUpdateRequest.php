<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
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
            'name' => ['required'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable'],
            'phone' => ['nullable', 'numeric'],
            'city_id' => ['nullable'],
            'timezone' => ['required'],
            'setting' => ['required','numeric']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'address' => 'Alamat',
            'phone' => 'Nomor telpon',
            'city_id' => 'Kota',
            'setting'=> 'Durasi pengambilan'
        ];
    }
}
