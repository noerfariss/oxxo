<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RestCreateRequest extends FormRequest
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
            'address' => ['nullable'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'accuracy' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'address' => 'Alamat',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
            'dates' => Carbon::now()->isoFormat('YYYY-MM-DD'),
            'time' => Carbon::now()->isoFormat('HH:mm:ss'),
        ]);
    }
}
