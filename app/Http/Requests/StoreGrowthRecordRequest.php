<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGrowthRecordRequest extends FormRequest
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
            'recorded_date' => 'required|date',
            'age_days' => 'required|integer|min:0',
            'average_weight_grams' => 'required|numeric|min:0',
            'is_below_expected' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}