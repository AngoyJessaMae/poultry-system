<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Batch;
use App\Models\FeedingLog;

class StoreFeedingLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isWorker() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $batch = $this->route('batch');
        $station = $batch instanceof Batch ? $batch->station : null;

        if (! $station) {
            $feedingLog = $this->route('feedingLog');
            $feedingLog = $feedingLog instanceof FeedingLog ? $feedingLog->loadMissing('station') : null;
            $station = $feedingLog?->station;
        }

        $feedType = $station?->required_feed_type;

        return [
            'unit' => ['required', 'string', 'in:kg,g,lb'],
            'feed_type' => array_values(array_filter([
                'required',
                'string',
                'max:255',
                $feedType ? Rule::in([$feedType]) : null,
            ])),
            'quantity' => ['required', 'numeric', 'gt:0'],
            'scheduled_at' => ['nullable', 'date'],
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'feed_type.in' => 'This station only uses the assigned feed type.',
        ];
    }
}
