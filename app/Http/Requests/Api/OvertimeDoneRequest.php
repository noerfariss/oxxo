<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class OvertimeDoneRequest extends FormRequest
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
            'address_out' => ['nullable'],
            'lat_out' => ['nullable'],
            'lon_out' => ['nullable'],
            'accuracy_out' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'address_out' => 'Alamat',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
            'time_out' => Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'),
        ]);
    }
}
