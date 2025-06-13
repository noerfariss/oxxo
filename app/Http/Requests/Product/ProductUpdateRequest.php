<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'category_id' => ['required'],
            'name' => ['required', 'min:3'],
            'unit' => ['required'],
            'price' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Produk',
            'category_id' => 'Kategori',
            'price' => 'Harga'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->status ? true : false,
        ]);
    }
}
