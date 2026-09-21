<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProductRequest extends StoreProductRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.update');
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['sku'] = [
            'nullable', 'string', 'max:100',
            Rule::unique('products')
                ->where('company_id', $this->user()->company_id)
                ->ignore($this->route('product')),
        ];

        return $rules;
    }
}
