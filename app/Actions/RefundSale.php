<?php

namespace App\Actions;

use App\Enums\CashMovementType;
use App\Enums\InventoryMovementType;
use App\Enums\SaleStatus;
use App\Models\CashMovement;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\User;
use App\Services\InventoryService;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundSale
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * @param  array<int, array{sale_item_id: int, quantity: float}>  $items
     */
    public function handle(Sale $sale, array $items, User $user, ?string $reason = null, ?string $method = null): Refund
    {
        return DB::transaction(function () use ($sale, $items, $user, $reason, $method) {
            $sale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            if ($sale->status !== SaleStatus::Completed && $sale->status !== SaleStatus::PartiallyRefunded) {
                throw ValidationException::withMessages(['sale' => 'Only completed sales can be refunded.']);
            }

            $refund = new Refund([
                'sale_id' => $sale->id,
                'amount' => 0,
                'reason' => $reason,
                'method' => $method ?? $sale->payments()->first()?->method?->value,
                'user_id' => $user->id,
            ]);
            $refund->save();

            $refundTotal = 0.0;
            $fullyRefunded = true;

            foreach ($items as $input) {
                $item = $sale->items()->whereKey($input['sale_item_id'])->with('product')->firstOrFail();
                $qty = (float) $input['quantity'];
                $refundable = (float) $item->quantity - (float) $item->quantity_refunded;

                if ($qty <= 0 || $qty > $refundable) {
                    throw ValidationException::withMessages([
                        'items' => "Cannot refund {$qty} of {$item->name}; {$refundable} refundable.",
                    ]);
                }

                // proportional amount incl. tax
                $amount = $refundable > 0 ? round((float) $item->total * ($qty / (float) $item->quantity), 2) : 0;
                $refundTotal += $amount;

                $item->quantity_refunded = (float) $item->quantity_refunded + $qty;
                $item->save();

                $refund->items()->create([
                    'sale_item_id' => $item->id,
                    'quantity' => $qty,
                    'amount' => $amount,
                ]);

                if ($item->product?->track_stock) {
                    $this->inventory->recordMovement(
                        product: $item->product,
                        branchId: $sale->branch_id,
                        quantity: $qty,
                        type: InventoryMovementType::Return_,
                        reference: $refund,
                        variantId: $item->product_variant_id,
                        userId: $user->id,
                        allowNegative: true,
                        note: 'Refund '.$sale->number,
                    );
                }
            }

            foreach ($sale->items()->get() as $item) {
                if ((float) $item->quantity_refunded < (float) $item->quantity) {
                    $fullyRefunded = false;
                }
            }

            $refund->amount = $refundTotal;
            $refund->save();

            $sale->status = $fullyRefunded ? SaleStatus::Refunded : SaleStatus::PartiallyRefunded;
            $sale->save();

            if ($sale->cashier_shift_id && $refund->method === 'cash') {
                CashMovement::create([
                    'cashier_shift_id' => $sale->cashier_shift_id,
                    'branch_id' => $sale->branch_id,
                    'type' => CashMovementType::Refund,
                    'amount' => -$refundTotal,
                    'reason' => 'Refund '.$sale->number,
                    'reference_type' => $refund->getMorphClass(),
                    'reference_id' => $refund->id,
                    'user_id' => $user->id,
                ]);
            }

            AuditLogger::log('sale.refunded', $sale, new: [
                'refund_id' => $refund->id,
                'amount' => $refundTotal,
            ]);

            return $refund;
        });
    }
}
