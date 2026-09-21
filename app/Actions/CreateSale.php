<?php

namespace App\Actions;

use App\Enums\CashMovementType;
use App\Enums\InventoryMovementType;
use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Models\CashMovement;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PricingService;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSale
{
    public function __construct(
        private PricingService $pricing,
        private InventoryService $inventory,
    ) {}

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

            $total = $priced['total'];
            $payments = is_array($data['payments'] ?? null) ? $data['payments'] : [];
            $paidTotal = round(collect($payments)->sum('amount'), 2);

            if ($paidTotal + 0.0001 < $total) {
                throw ValidationException::withMessages([
                    'payments' => "Payments ({$paidTotal}) do not cover the total ({$total}).",
                ]);
            }

            $shift = $cashier->currentShift();

            // Resume a held sale or create a new one.
            $sale = isset($data['held_sale_id'])
                ? Sale::whereKey($data['held_sale_id'])->where('status', SaleStatus::Held)->lockForUpdate()->firstOrFail()
                : new Sale;

            $sale->fill([
                'company_id' => $cashier->company_id,
                'branch_id' => $branchId,
                'cashier_shift_id' => $shift?->id,
                'user_id' => $cashier->id,
                'customer_id' => $data['customer_id'] ?? null,
                'status' => SaleStatus::Completed,
                'subtotal' => $priced['subtotal'],
                'discount_total' => $priced['discount_total'],
                'tax_total' => $priced['tax_total'],
                'total' => $total,
                'paid_total' => $paidTotal,
                'discount_type' => $data['discount']['type'] ?? null,
                'discount_value' => $data['discount']['value'] ?? 0,
                'note' => $data['note'] ?? null,
                'completed_at' => now(),
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

                if ($line['product']->track_stock) {
                    $this->inventory->recordMovement(
                        product: $line['product'],
                        branchId: $branchId,
                        quantity: -$line['quantity'],
                        type: InventoryMovementType::Sale,
                        reference: $sale,
                        unitCost: $line['unit_cost'],
                        variantId: $line['variant']?->id,
                        userId: $cashier->id,
                        allowNegative: (bool) ($data['allow_negative_stock'] ?? false),
                    );
                }
            }

            $changePool = round($paidTotal - $total, 2);

            foreach ($payments as $payment) {
                $method = PaymentMethod::from($payment['method']);
                $change = 0.0;

                if ($method === PaymentMethod::Cash && $changePool > 0) {
                    $change = $changePool;
                    $changePool = 0;
                }

                $sale->payments()->create([
                    'method' => $method,
                    'amount' => round((float) $payment['amount'], 2) - ($method === PaymentMethod::Cash ? $change : 0),
                    'tendered' => $payment['amount'],
                    'change' => $change,
                    'reference' => $payment['reference'] ?? null,
                    'user_id' => $cashier->id,
                ]);

                if ($method === PaymentMethod::Cash && $shift) {
                    CashMovement::create([
                        'cashier_shift_id' => $shift->id,
                        'branch_id' => $branchId,
                        'type' => CashMovementType::Sale,
                        'amount' => round((float) $payment['amount'], 2) - $change,
                        'reference_type' => $sale->getMorphClass(),
                        'reference_id' => $sale->id,
                        'user_id' => $cashier->id,
                    ]);
                }
            }

            $sale->change_total = round($paidTotal - $total, 2);
            $sale->save();

            $sale->receipt()->delete();
            $sale->receipt()->create([
                'number' => 'R-'.$sale->number,
                'payload' => $sale->load(['items', 'payments', 'cashier', 'customer', 'branch'])->toArray(),
            ]);

            AuditLogger::log('sale.completed', $sale, new: [
                'number' => $sale->number,
                'total' => $total,
                'items' => count($priced['items']),
            ]);

            return $sale->refresh();
        });
    }
}
