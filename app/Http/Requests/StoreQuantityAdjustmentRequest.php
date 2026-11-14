<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuantityAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManager() === true;
    }

    public function rules(): array
    {
        return [
            'previous_quantity' => ['required', 'integer', 'min:0'],
            'corrected_quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }
}
