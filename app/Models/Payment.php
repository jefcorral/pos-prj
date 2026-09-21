<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['sale_id', 'method', 'amount', 'tendered', 'change', 'reference', 'user_id'];

    protected $casts = [
        'method' => PaymentMethod::class,
        'amount' => 'decimal:2',
        'tendered' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    /** @return BelongsTo<Sale, $this> */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
