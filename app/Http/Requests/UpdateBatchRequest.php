<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
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
            'batch_code' => 'sometimes|string|max:255',
            'station_id' => 'sometimes|exists:stations,id',
            'arrival_date' => 'sometimes|date',
            'initial_quantity' => 'sometimes|integer|min:1',
            'current_quantity' => 'sometimes|integer|min:0',
            'initial_weight_grams' => 'sometimes|numeric|min:0',
            'feeding_method' => 'sometimes|string',
            'status' => 'sometimes|string',
            'is_below_expected' => 'sometimes|boolean',
            'below_expected_reason' => 'nullable|string',
        ];
    }
}