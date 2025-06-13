<?php

namespace App\Http\Requests\Office;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfficeCreateRequest extends FormRequest
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
            'name' => ['required', 'min:3', Rule::unique('offices')],
            'address' => ['required', 'min:3'],
            'city_id' => ['required'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'city_id' => 'Kota',
            'address' => 'Alamat',
            'name' => 'Outlet/Cabang'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'wfh' => $this->wfh ? true : false,
        ]);
    }
}
