<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('sales.refund');
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_item_id' => ['required', 'integer', 'exists:sale_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'reason' => ['nullable', 'string', 'max:500'],
            'method' => ['nullable', 'string', 'max:50'],
        ];
    }
}
