<?php

namespace App\Http\Requests\Api;

use App\Http\Resources\Api\Member\MemberResource;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ReceiptCreateRequest extends FormRequest
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
            'nominal' => ['required', 'numeric', 'min:50'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'member_id' => request()->user()->id,
            'members' => json_encode(new MemberResource(request()->user())),
            'dates' => date('Y-m-d')
        ]);
    }

    protected function passedValidation()
    {
        $setting = Setting::query()->select('setting')->first()->setting;
        $setting = json_decode($setting);

        $max = $setting->receipt_max;
        $nominal = $this->nominal;

        if ($nominal > $max) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal yang diajukan lebih dari Rp ' . number_format($max, 0, ',', '.'),
            ]);
        }
    }
}
