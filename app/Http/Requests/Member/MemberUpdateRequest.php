<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberUpdateRequest extends FormRequest
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
            'office_id' => ['required'],
            'kios_id' => ['required'],
            'name' => ['required'],
            'phone' => ['required', 'numeric', Rule::unique('members')->ignore($this->member)],
            'born' => ['nullable', 'date'],
            'address' => ['required', 'min:3'],
            'city_id' => ['nullable'],
            'gender' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->status ? true : false,
            'is_member' => $this->is_member ? true : false,
        ]);
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'phone' => 'Whatsapp',
            'gender' => 'Jenis kelamin',
            'office_id' => 'Outlet',
            'kios_id' => 'Kios',
            'address' => 'Alamat'
        ];
    }
}
