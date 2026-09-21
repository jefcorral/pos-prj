<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Atomically adjust stock for a product/variant at a branch and record the
     * movement in the ledger. Must be called inside a DB transaction.
     */
    public function recordMovement(
        Product $product,
        int $branchId,
        float $quantity, // signed
        InventoryMovementType $type,
        ?Model $reference = null,
        ?float $unitCost = null,
        ?string $note = null,
        ?int $variantId = null,
        ?int $userId = null,
        bool $allowNegative = false,
    ): InventoryMovement {
        $inventory = Inventory::where('branch_id', $branchId)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->lockForUpdate()
            ->first();

        if (! $inventory) {
            $inventory = new Inventory([
                'company_id' => $product->company_id,
                'branch_id' => $branchId,
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => 0,
            ]);
        }

        $newQuantity = (float) $inventory->quantity + $quantity;

        if ($product->track_stock && ! $allowNegative && $newQuantity < 0) {
            throw ValidationException::withMessages([
                'items' => "Insufficient stock for {$product->name}. Available: {$inventory->quantity}.",
            ]);
        }

        $inventory->quantity = $newQuantity;
        $inventory->save();

        return InventoryMovement::create([
            'company_id' => $product->company_id,
            'branch_id' => $branchId,
            'product_id' => $product->id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'quantity_after' => $newQuantity,
            'type' => $type,
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
            'unit_cost' => $unitCost,
            'note' => $note,
            'user_id' => $userId,
        ]);
    }

    public function currentStock(int $productId, int $branchId, ?int $variantId = null): float
    {
        $inventory = Inventory::where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        return (float) ($inventory?->quantity ?? 0);
    }
}
