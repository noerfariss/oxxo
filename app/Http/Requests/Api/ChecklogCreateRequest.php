<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ChecklogCreateRequest extends FormRequest
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
            'time_in' => ['required'],
            'address_in' => ['nullable'],
            'lat_in' => ['nullable'],
            'lon_in' => ['nullable'],
            'accuracy_in' => ['nullable'],
            'photo_in' => ['required', 'mimetypes:image/png,image/jpg,image/jpeg', 'max:7000'],
            'reason_late' => ['nullable']
        ];
    }

    public function attributes()
    {
        return [
            'address_in' => 'Alamat',
            'lat_in' => 'Latitude',
            'lon_in' => 'Longitude',
            'accuracy_in' => 'Akurasi',
            'photo_in' => 'Foto',
            'reason_late' => 'Alasan'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
            'dates' => Carbon::now()->isoFormat('YYYY-MM-DD'),
            'time_in' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
        ]);
    }
}
