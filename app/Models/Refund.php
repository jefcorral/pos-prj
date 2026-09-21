<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refund extends Model
{
    protected $fillable = ['sale_id', 'amount', 'reason', 'method', 'user_id'];

    protected $casts = ['amount' => 'decimal:2'];

    /** @return BelongsTo<Sale, $this> */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /** @return HasMany<RefundItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(RefundItem::class);
    }
}
