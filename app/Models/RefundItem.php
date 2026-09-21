<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundItem extends Model
{
    protected $fillable = ['refund_id', 'sale_item_id', 'quantity', 'amount'];

    protected $casts = ['quantity' => 'decimal:3', 'amount' => 'decimal:2'];

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }
}
