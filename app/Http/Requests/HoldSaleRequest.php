<?php

namespace App\Http\Requests;

class HoldSaleRequest extends CheckoutRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['payments'], $rules['payments.*.method'], $rules['payments.*.amount'], $rules['payments.*.reference']);
        $rules['held_sale_id'] = ['nullable', 'integer', 'exists:sales,id'];

        return $rules;
    }
}
