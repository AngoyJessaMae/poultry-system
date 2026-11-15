<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Station;

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
            'station_code' => ['required', 'string', Rule::in(Station::ALLOWED_CODES)],
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

    public function messages(): array
    {
        return [
            'station_code.in' => 'Please choose one of the supported station identifiers: S1, S2, or S3.',
        ];
    }
}
