<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBatchRequest extends FormRequest
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
            'batch_code' => 'required|string|max:255|unique:batches,batch_code,' . $this->route('batch')?->id,
            'station_id' => 'required|exists:stations,id',
            'arrival_date' => 'required|date',
            'initial_quantity' => 'required|integer|min:1',
            'initial_weight_grams' => 'required|numeric|min:0',
            'feeding_method' => ['required', Rule::in(['twice_daily', 'four_times_daily', 'unlimited'])],
            'status' => ['required', Rule::in(['active', 'sold_out', 'archived'])],
        ];
    }
}