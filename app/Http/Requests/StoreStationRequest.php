<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
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
            'station_code' => ['required', 'string', 'max:50', 'regex:/^S[A-Z0-9-]+$/i'],
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'min_age_days' => 'required|integer|min:0',
            'max_age_days' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['station_code' => strtoupper(trim((string) $this->station_code))]);
    }
}
