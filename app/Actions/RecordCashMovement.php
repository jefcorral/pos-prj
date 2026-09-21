<?php

namespace App\Actions;

use App\Enums\CashMovementType;
use App\Models\CashierShift;
use App\Models\CashMovement;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordCashMovement
{
    public function handle(CashierShift $shift, CashMovementType $type, float $amount, User $user, ?string $reason = null): CashMovement
    {
        return DB::transaction(function () use ($shift, $type, $amount, $user, $reason) {
            if ($shift->status !== 'open') {
                throw ValidationException::withMessages(['shift' => 'The shift is closed.']);
            }

            if (! in_array($type, [CashMovementType::In, CashMovementType::Out])) {
                throw ValidationException::withMessages(['type' => 'Only manual cash in/out is allowed.']);
            }

            $movement = CashMovement::create([
                'cashier_shift_id' => $shift->id,
                'branch_id' => $shift->branch_id,
                'type' => $type,
                'amount' => $type === CashMovementType::Out ? -abs($amount) : abs($amount),
                'reason' => $reason,
                'user_id' => $user->id,
            ]);

            AuditLogger::log('cash.'.$type->value, $movement, new: ['amount' => $amount, 'reason' => $reason]);

            return $movement;
        });
    }
}
