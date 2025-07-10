<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ValidationDataRequest extends FormRequest
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
            'name' => ['required', 'min:3'],
            'gender' => ['required'],
            'address' => ['required', 'min:6'],
            'state' => ['required'],
            'city' => ['required'],
            'district_id' => ['required']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'gender' => 'Jenis kelamin',
            'address' => 'Alamat',
            'state' => 'Provinsi',
            'city' => 'Kota',
            'district_id' => 'Kecamatan'
        ];
    }
}
