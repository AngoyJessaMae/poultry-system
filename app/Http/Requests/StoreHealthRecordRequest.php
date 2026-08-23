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
            'record_type' => ['required', Rule::in(['symptom', 'diagnosis', 'medication', 'recommendation'])],
            'description' => 'required|string',
            'medication_given' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'recorded_at' => 'required|date',
        ];
    }
}