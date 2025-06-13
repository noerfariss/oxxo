<?php

namespace App\Http\Requests\Kios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KiosUpdateRequest extends FormRequest
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
            'name' => ['required', 'min:3', Rule::unique('outlet_kios')->ignore($this->kios)],
            'address' => ['required', 'min:3'],
            'city_id' => ['required'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'modaloutlet' => ['nullable']
        ];
    }

    public function attributes()
    {
        return [
            'city_id' => 'Kota',
            'address' => 'Alamat',
            'name' => 'Nama kios'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->status ? true : false,
            'office_id' => $this->modaloutlet ? $this->modaloutlet[0] : null
        ]);
    }
}
