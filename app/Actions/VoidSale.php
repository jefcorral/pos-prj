<?php

namespace App\Actions;

use App\Enums\CashMovementType;
use App\Enums\InventoryMovementType;
use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Models\CashMovement;
use App\Models\Sale;
use App\Models\User;
use App\Services\InventoryService;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoidSale
{
    public function __construct(private InventoryService $inventory) {}

    public function handle(Sale $sale, User $user, ?string $reason = null): Sale
    {
        return DB::transaction(function () use ($sale, $user, $reason) {
            $sale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            if (in_array($sale->status, [SaleStatus::Voided, SaleStatus::Refunded])) {
                throw ValidationException::withMessages(['sale' => 'This sale cannot be voided.']);
            }

            $old = $sale->only('status', 'total');

            if ($sale->status === SaleStatus::Completed) {
                // Restock items
                foreach ($sale->items()->with('product')->get() as $item) {
                    if ($item->product?->track_stock) {
                        $this->inventory->recordMovement(
                            product: $item->product,
                            branchId: $sale->branch_id,
                            quantity: (float) $item->quantity - (float) $item->quantity_refunded,
                            type: InventoryMovementType::Return_,
                            reference: $sale,
                            variantId: $item->product_variant_id,
                            userId: $user->id,
                            allowNegative: true,
                            note: 'Void sale '.$sale->number,
                        );
                    }
                }

                // Reverse cash
                if ($sale->cashier_shift_id) {
                    $cashPaid = $sale->payments()->where('method', PaymentMethod::Cash)->sum('amount');
                    if ($cashPaid > 0) {
                        CashMovement::create([
                            'cashier_shift_id' => $sale->cashier_shift_id,
                            'branch_id' => $sale->branch_id,
                            'type' => CashMovementType::Refund,
                            'amount' => -$cashPaid,
                            'reason' => 'Void sale '.$sale->number,
                            'reference_type' => $sale->getMorphClass(),
                            'reference_id' => $sale->id,
                            'user_id' => $user->id,
                        ]);
                    }
                }
            }

            $sale->update([
                'status' => SaleStatus::Voided,
                'voided_at' => now(),
                'voided_by' => $user->id,
                'void_reason' => $reason,
            ]);

            AuditLogger::log('sale.voided', $sale, old: $old, new: ['reason' => $reason]);

            return $sale->refresh();
        });
    }
}
