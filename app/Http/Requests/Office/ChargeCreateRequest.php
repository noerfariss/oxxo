<?php

namespace App\Http\Requests\Office;

use Illuminate\Foundation\Http\FormRequest;

class ChargeCreateRequest extends FormRequest
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
            'nominal_charge.*' => ['required', 'numeric'],
            'start_charge.*' => ['required', 'numeric'],
            'end_charge.*' => ['required', 'numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'nominal_charge' => 'Nominal',
            'start_charge' => 'Menit mulai',
            'end_charge' => 'Menit akhir',
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge([
            'updatestatus' => $this->updatestatus === 'true' ? true : false,
            'workingtimeuuid' => $this->workingtimeuuid ? $this->workingtimeuuid : null,
        ]);
    }
}
