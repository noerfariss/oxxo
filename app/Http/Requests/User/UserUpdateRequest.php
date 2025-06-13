<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id, 'id')],
            'whatsapp' => ['required', 'numeric'],
            'photo' => ['nullable'],
            'address' => ['nullable'],
            'roles' => ['required']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'address' => 'Alamat',
            'photo' => 'Foto'
        ];
    }
}
