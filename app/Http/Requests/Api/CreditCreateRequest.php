<?php

namespace App\Http\Requests\Api;

use App\Http\Resources\Api\Member\MemberResource;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreditCreateRequest extends FormRequest
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
            'nominal' => ['required', 'numeric', 'min:100000'],
            'description' => ['required', 'min:3'],
            'tenor' => ['required', 'numeric']
        ];
    }

    public function attributes()
    {
        return [
            'description' => 'Keterangan'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
            'members' => json_encode(new MemberResource(request()->user())),
        ]);
    }
}
