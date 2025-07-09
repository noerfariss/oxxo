<?php

namespace App\Http\Requests\Member;

use App\Class\MemberClass;
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
            'name' => ['required'],
            'phone' => ['required', 'numeric', Rule::unique('members', 'phone')],
            'born' => ['required', 'date'],
            'address' => ['required', 'min:3'],
            'city_id' => ['nullable'],
            'gender' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'password' => Hash::make('123456'),
            'numberid' => MemberClass::generateNumber(),
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
            'address' => 'Alamat'
        ];
    }
}
