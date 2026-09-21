<?php

namespace App\Actions;

use App\Models\CashierShift;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpenCashierShift
{
    public function handle(User $user, int $branchId, float $openingCash, ?string $note = null): CashierShift
    {
        return DB::transaction(function () use ($user, $branchId, $openingCash, $note) {
            $existing = CashierShift::where('user_id', $user->id)
                ->where('branch_id', $branchId)
                ->open()
                ->lockForUpdate()
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages(['shift' => 'You already have an open shift at this branch.']);
            }

            $shift = CashierShift::create([
                'company_id' => $user->company_id,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'opening_cash' => $openingCash,
                'status' => 'open',
                'note' => $note,
                'opened_at' => now(),
            ]);

            AuditLogger::log('shift.opened', $shift, new: ['opening_cash' => $openingCash]);

            return $shift;
        });
    }
}
