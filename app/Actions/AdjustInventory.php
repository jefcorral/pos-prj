<?php

namespace App\Actions;

use App\Enums\InventoryMovementType;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Services\InventoryService;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;

class AdjustInventory
{
    public function __construct(private InventoryService $inventory) {}

    public function handle(
        Product $product,
        int $branchId,
        float $quantity, // signed
        string $reason,
        User $user,
        ?int $variantId = null,
        ?string $note = null,
        bool $allowNegative = false,
    ): StockAdjustment {
        return DB::transaction(function () use ($product, $branchId, $quantity, $reason, $user, $variantId, $note, $allowNegative) {
            $adjustment = StockAdjustment::create([
                'company_id' => $product->company_id,
                'branch_id' => $branchId,
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'reason' => $reason,
                'note' => $note,
                'user_id' => $user->id,
            ]);

            $movement = $this->inventory->recordMovement(
                product: $product,
                branchId: $branchId,
                quantity: $quantity,
                type: InventoryMovementType::Adjustment,
                reference: $adjustment,
                variantId: $variantId,
                userId: $user->id,
                allowNegative: $allowNegative,
            );

            AuditLogger::log('inventory.adjusted', $adjustment, new: [
                'product' => $product->name,
                'quantity' => $quantity,
                'reason' => $reason,
                'quantity_after' => (float) $movement->quantity_after,
            ]);

            return $adjustment;
        });
    }
}
