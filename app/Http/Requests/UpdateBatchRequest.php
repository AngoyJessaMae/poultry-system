<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Batch;
use App\Models\Station;
use App\Services\StationFeedingService;

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
            'farm_name' => 'sometimes|nullable|string|max:255',
            'station_id' => 'sometimes|exists:stations,id',
            'flockman_id' => 'sometimes|nullable|exists:users,id',
            'arrival_date' => 'sometimes|date',
            'initial_weight_grams' => 'sometimes|numeric|min:0',
            'feeding_method' => 'sometimes|string',
            'status' => 'sometimes|string',
            'is_below_expected' => 'sometimes|boolean',
            'below_expected_reason' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $batch = $this->route('batch');
        $batch = $batch instanceof Batch ? $batch : Batch::find($batch);
        $station = $this->station_id
            ? Station::find($this->station_id)
            : $batch?->station;

        if ($station) {
            $this->merge([
                'feeding_method' => app(StationFeedingService::class)->configFor($station)['method'],
            ]);
        }
    }
}
