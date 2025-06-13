<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCreateRequest extends FormRequest
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
            'dates' => ['required'],
            'type' => ['required'],
            'description' => ['required', 'min:3'],
            'photo' => ['required_if:type,2', 'max:7000', 'mimetypes:image/png,image/jpg,image/jpeg'],
        ];
    }

    public function attributes()
    {
        return [
            'photo' => 'Foto',
            'description' => 'Keterangan',
            'dates' => 'Tanggal',
            'type' => 'Tipe'
        ];
    }

    public function messages()
    {
        return [
            'photo.required_if' => 'Foto wajib diisi jika tipe Sakit', // Menyesuaikan pesan kesalahan untuk aturan 'required_if'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
        ]);
    }
}
