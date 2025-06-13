<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class OvertimeCreateRequest extends FormRequest
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
            'address_in' => ['nullable'],
            'lat_in' => ['nullable'],
            'lon_in' => ['nullable'],
            'accuracy_in' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'address_in' => 'Alamat',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
            'dates' => Carbon::now()->isoFormat('YYYY-MM-DD'),
            'time_in' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
        ]);
    }
}
