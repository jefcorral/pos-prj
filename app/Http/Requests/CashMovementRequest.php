<?php

namespace App\Http\Requests;

use App\Enums\CashMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('shifts.cash');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in([CashMovementType::In->value, CashMovementType::Out->value])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}
