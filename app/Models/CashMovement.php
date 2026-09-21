<?php

namespace App\Models;

use App\Enums\CashMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashMovement extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'cashier_shift_id', 'branch_id', 'type', 'amount', 'reason',
        'reference_type', 'reference_id', 'user_id', 'created_at',
    ];

    protected $casts = [
        'type' => CashMovementType::class,
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /** @return BelongsTo<CashierShift, $this> */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    /** @return MorphTo<Model, $this> */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
