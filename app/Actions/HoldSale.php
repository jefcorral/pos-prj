<?php

namespace App\Actions;

use App\Enums\SaleStatus;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\User;
use App\Services\PricingService;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HoldSale
{
    public function __construct(private PricingService $pricing) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, User $cashier, int $branchId): Sale
    {
        return DB::transaction(function () use ($data, $cashier, $branchId) {
            $items = is_array($data['items'] ?? null) ? $data['items'] : [];
            $discount = is_array($data['discount'] ?? null) ? $data['discount'] : null;
            $priced = $this->pricing->priceCart($items, $discount);

            if (empty($priced['items'])) {
                throw ValidationException::withMessages(['items' => 'The cart is empty.']);
            }

            $sale = isset($data['held_sale_id'])
                ? Sale::whereKey($data['held_sale_id'])->where('status', SaleStatus::Held)->lockForUpdate()->firstOrFail()
                : new Sale;

            $sale->fill([
                'company_id' => $cashier->company_id,
                'branch_id' => $branchId,
                'cashier_shift_id' => $cashier->currentShift()?->id,
                'user_id' => $cashier->id,
                'customer_id' => $data['customer_id'] ?? null,
                'status' => SaleStatus::Held,
                'subtotal' => $priced['subtotal'],
                'discount_total' => $priced['discount_total'],
                'tax_total' => $priced['tax_total'],
                'total' => $priced['total'],
                'discount_type' => $data['discount']['type'] ?? null,
                'discount_value' => $data['discount']['value'] ?? 0,
                'note' => $data['note'] ?? null,
                'held_at' => now(),
            ]);
            $sale->save();

            $sale->items()->delete();
            foreach ($priced['items'] as $line) {
                $sale->items()->create([
                    'product_id' => $line['product']->id,
                    'product_variant_id' => $line['variant']?->id,
                    'name' => $line['product']->name.($line['variant'] ? " ({$line['variant']->name})" : ''),
                    'sku' => $line['variant'] instanceof ProductVariant ? ($line['variant']->sku ?? $line['product']->sku) : $line['product']->sku,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'unit_cost' => $line['unit_cost'],
                    'discount' => $line['discount'],
                    'tax' => $line['tax'],
                    'total' => $line['total'],
                ]);
            }

            AuditLogger::log('sale.held', $sale, new: ['number' => $sale->number]);

            return $sale->refresh();
        });
    }
}
