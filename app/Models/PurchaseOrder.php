<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'supplier_id', 'user_id',
        'reference_no', 'status', 'expected_at', 'total', 'notes',
    ];

    protected $casts = ['expected_at' => 'date', 'total' => 'decimal:2'];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $po) {
            $po->public_id ??= (string) str()->ulid();
            $po->reference_no ??= 'PO-'.now()->format('Ymd').'-'.str()->upper(str()->random(6));
        });
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /** @return HasMany<PurchaseOrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /** @return HasMany<GoodsReceipt, $this> */
    public function receipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->items()->sum('total');
        $this->save();
    }
}
