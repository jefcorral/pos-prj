<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

/**
 * Server-side cart pricing. The frontend never sends totals — only item ids,
 * quantities and discount intents. All money math happens here.
 */
class PricingService
{
    /**
     * @param  array<int, array{product_id:int, product_variant_id?:int|null, quantity:float, discount?:float}>  $items
     * @param  array{type?:string, value?:float}|null  $discount  cart-level discount
     * @return array{items: array<int, array>, subtotal: float, discount_total: float, tax_total: float, total: float}
     */
    public function priceCart(array $items, ?array $discount = null): array
    {
        $lines = [];
        $subtotal = 0.0;

        foreach ($items as $input) {
            $product = Product::with('tax')->findOrFail($input['product_id']);
            $variant = isset($input['product_variant_id']) && $input['product_variant_id']
                ? ProductVariant::findOrFail($input['product_variant_id'])
                : null;

            $unitPrice = (float) ($variant?->selling_price ?? $product->selling_price);
            $unitCost = (float) ($variant?->cost_price ?? $product->cost_price);
            $quantity = (float) $input['quantity'];
            $lineDiscount = round(min((float) ($input['discount'] ?? 0), $unitPrice * $quantity), 2);

            $gross = round($unitPrice * $quantity - $lineDiscount, 2);
            $subtotal += $gross;

            $lines[] = [
                'product' => $product,
                'variant' => $variant,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'discount' => $lineDiscount,
                'gross' => $gross,
            ];
        }

        $subtotal = round($subtotal, 2);

        $discountTotal = 0.0;
        if ($discount && ($discount['value'] ?? 0) > 0) {
            $discountTotal = ($discount['type'] ?? 'fixed') === 'percent'
                ? round($subtotal * (float) $discount['value'] / 100, 2)
                : round((float) $discount['value'], 2);
            $discountTotal = min($discountTotal, $subtotal);
        }

        $taxTotal = 0.0;
        $exclusiveTax = 0.0;

        foreach ($lines as $i => $line) {
            $tax = $line['product']->tax;
            $rate = $tax ? (float) $tax->rate : 0.0;

            // allocate the cart discount pro-rata across lines
            $share = $subtotal > 0 ? $line['gross'] / $subtotal : 0;
            $taxable = round($line['gross'] - $discountTotal * $share, 2);

            if ($tax && $rate > 0) {
                if ($tax->type === 'inclusive') {
                    $lineTax = round($taxable - $taxable / (1 + $rate / 100), 2);
                } else {
                    $lineTax = round($taxable * $rate / 100, 2);
                    $exclusiveTax += $lineTax;
                }
            } else {
                $lineTax = 0.0;
            }

            $taxTotal += $lineTax;
            $lines[$i]['tax'] = $lineTax;
            $lines[$i]['total'] = round($taxable + ($tax?->type === 'inclusive' ? 0 : $lineTax), 2);
        }

        return [
            'items' => $lines,
            'subtotal' => $subtotal,
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'total' => round($subtotal - $discountTotal + $exclusiveTax, 2),
        ];
    }
}
