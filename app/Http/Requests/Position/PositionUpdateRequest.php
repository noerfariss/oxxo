<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PositionUpdateRequest extends FormRequest
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
            'name' => ['required', Rule::unique('positions', 'name')->ignore($this->position)],
            'status' => ['nullable']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Jabatan'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->status ? true : false,
        ]);
    }
}
