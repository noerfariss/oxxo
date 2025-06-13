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
            'division_id' => ['required'],
            'position_id' => ['required'],
            'nik' => ['required', Rule::unique('members')->ignore($this->member)],
            'email' => ['required', 'email', Rule::unique('members')->ignore($this->member)],
            'name' => ['required'],
            'phone' => ['required', 'numeric', Rule::unique('members')->ignore($this->member)],
            'gender' => ['required'],
            'status' => ['nullable']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->status ? true : false,
        ]);
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'phone' => 'Whatsapp',
            'gender' => 'Jenis kelamin',
            'office_id' => 'Kantor',
            'division_id' => 'Divisi',
            'position_id' => 'Jabatan'
        ];
    }
}
