<?php

namespace App\Http\Requests\Member;

use App\Trait\GlobalTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberCreateRequest extends FormRequest
{
    use GlobalTrait;

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
            'nik' => ['nullable', Rule::unique('members', 'nik')],
            'email' => ['required', 'email', Rule::unique('members', 'email')],
            'name' => ['required'],
            'phone' => ['required', 'numeric', Rule::unique('members', 'phone')],
            'gender' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'password' => Hash::make('123456'),
        ]);
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'gender' => 'Jenis kelamin',
            'office_id' => 'Kantor',
            'division_id' => 'Divisi',
            'position_id' => 'Jabatan'
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'nik' => $this->nik ? $this->nik : $this->GenerateNumberMember(),
        ]);
    }
}
