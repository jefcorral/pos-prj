<?php

namespace App\Actions;

use App\Enums\InventoryMovementType;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\InventoryService;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReceiveGoods
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * @param  array<int, array{purchase_order_item_id: int, quantity: float}>  $items
     */
    public function handle(PurchaseOrder $po, array $items, User $user, ?string $note = null): GoodsReceipt
    {
        return DB::transaction(function () use ($po, $items, $user, $note) {
            $po = PurchaseOrder::whereKey($po->id)->lockForUpdate()->firstOrFail();

            if (in_array($po->status, ['received', 'cancelled'])) {
                throw ValidationException::withMessages(['po' => "Purchase order is {$po->status}."]);
            }

            $receipt = GoodsReceipt::create([
                'purchase_order_id' => $po->id,
                'branch_id' => $po->branch_id,
                'user_id' => $user->id,
                'note' => $note,
                'received_at' => now(),
            ]);

            foreach ($items as $input) {
                $poItem = $po->items()->whereKey($input['purchase_order_item_id'])->with('product')->firstOrFail();
                $qty = (float) $input['quantity'];
                $outstanding = $poItem->quantityOutstanding();

                if ($qty <= 0 || $qty > $outstanding) {
                    throw ValidationException::withMessages([
                        'items' => "Cannot receive {$qty}; only {$outstanding} outstanding for item #{$poItem->id}.",
                    ]);
                }

                $receipt->items()->create([
                    'purchase_order_item_id' => $poItem->id,
                    'quantity' => $qty,
                ]);

                $poItem->quantity_received = (float) $poItem->quantity_received + $qty;
                $poItem->save();

                $this->inventory->recordMovement(
                    product: $poItem->product,
                    branchId: $po->branch_id,
                    quantity: $qty,
                    type: InventoryMovementType::Purchase,
                    reference: $receipt,
                    unitCost: (float) $poItem->unit_cost,
                    variantId: $poItem->product_variant_id,
                    userId: $user->id,
                    allowNegative: true,
                    note: 'PO '.$po->reference_no,
                );
            }

            $allReceived = $po->items()->get()->every(
                fn ($item) => (float) $item->quantity_received >= (float) $item->quantity_ordered
            );
            $anyReceived = $po->items()->where('quantity_received', '>', 0)->exists();

            $po->status = $allReceived ? 'received' : ($anyReceived ? 'partially_received' : $po->status);
            $po->save();

            AuditLogger::log('po.received', $receipt, new: ['po' => $po->reference_no]);

            return $receipt;
        });
    }
}
