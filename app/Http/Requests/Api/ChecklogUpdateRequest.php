<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ChecklogUpdateRequest extends FormRequest
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
            'address_out' => ['nullable'],
            'lat_out' => ['nullable'],
            'lon_out' => ['nullable'],
            'accuracy_out' => ['nullable'],
            'photo_out' => ['required', 'mimetypes:image/png,image/jpg,image/jpeg', 'max:7000'],
        ];
    }

    public function attributes()
    {
        return [
            'address_out' => 'Alamat',
            'lat_out' => 'Latitude',
            'lon_out' => 'Longitude',
            'accuracy_out' => 'Akurasi',
            'photo_out' => 'Foto'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'time_out' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
        ]);
    }
}
