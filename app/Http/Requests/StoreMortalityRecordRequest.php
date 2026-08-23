<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMortalityRecordRequest extends FormRequest
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
            'station_id' => 'required|exists:stations,id',
            'mortality_date' => 'required|date',
            'count' => 'required|integer|min:1',
            'suspected_cause' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}