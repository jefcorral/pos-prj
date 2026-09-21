<?php

namespace App\Models;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'cashier_shift_id', 'user_id', 'customer_id',
        'number', 'status', 'subtotal', 'discount_total', 'tax_total', 'total',
        'paid_total', 'change_total', 'discount_type', 'discount_value', 'note',
        'held_at', 'completed_at', 'voided_at', 'voided_by', 'void_reason',
    ];

    protected $casts = [
        'status' => SaleStatus::class,
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'change_total' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'held_at' => 'datetime',
        'completed_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            $sale->public_id ??= (string) str()->ulid();
            $sale->number ??= static::nextNumber($sale->branch_id);
        });
    }

    public static function nextNumber(?int $branchId = null): string
    {
        $query = static::withoutGlobalScopes()->lockForUpdate();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        $count = $query->count() + 1;

        return 'S-'.now()->format('Ymd').'-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
