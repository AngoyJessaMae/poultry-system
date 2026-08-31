<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedingLogRequest extends FormRequest
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
            'feeding_time_slot' => 'nullable|string|max:255',
            'feed_type' => 'required|string|max:255',
            'quantity_kg' => 'required|numeric|min:0',
            'fed_at' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}