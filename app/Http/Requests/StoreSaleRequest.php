<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'batch_id' => 'required|exists:batches,id,status,active',
            'sale_date' => 'required|date',
            'heads_sold' => 'required|integer|min:1',
            'total_weight_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
        ];
    }
}