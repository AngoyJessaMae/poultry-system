<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHealthRecordRequest extends FormRequest
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
            'affected_count' => 'required|integer|min:1',
            'dead_count' => 'required|integer|min:0|lte:affected_count',
            'status' => ['required', Rule::in(['under_treatment', 'recovering', 'recovered', 'dead'])],
            'recorded_date' => 'required|date',
            'observation' => 'required|string',
            'medication_name' => 'nullable|string|max:255',
            'dosage_amount' => 'nullable|string|max:255',
            'dosage_unit' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'remarks' => 'nullable|string',
            'remedy' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('status') === 'dead' && (int) $this->input('dead_count') < 1) {
                $validator->errors()->add('dead_count', 'Enter at least one dead chicken when the status is Dead.');
            }
        });
    }
}