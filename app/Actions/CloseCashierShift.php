<?php

namespace App\Actions;

use App\Models\CashierShift;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CloseCashierShift
{
    public function handle(CashierShift $shift, User $user, float $actualCash, ?string $note = null): CashierShift
    {
        return DB::transaction(function () use ($shift, $actualCash, $note) {
            $shift = CashierShift::whereKey($shift->id)->lockForUpdate()->firstOrFail();

            if ($shift->status !== 'open') {
                throw ValidationException::withMessages(['shift' => 'This shift is already closed.']);
            }

            $movements = $shift->cashMovements()
                ->selectRaw("
                    coalesce(sum(case when type in ('sale','in') then amount else 0 end),0) as inflow,
                    coalesce(sum(case when type in ('refund','out') then amount else 0 end),0) as outflow
                ")
                ->first();

            $expected = round((float) $shift->opening_cash + (float) $movements->inflow + (float) $movements->outflow, 2);
            // refund/out amounts are stored negative, so they are already signed

            $shift->update([
                'status' => 'closed',
                'expected_cash' => $expected,
                'actual_cash' => $actualCash,
                'variance' => round($actualCash - $expected, 2),
                'closed_at' => now(),
                'note' => $note ? trim($shift->note."\n".$note) : $shift->note,
            ]);

            AuditLogger::log('shift.closed', $shift, new: [
                'expected_cash' => $expected,
                'actual_cash' => $actualCash,
                'variance' => $shift->variance,
            ]);

            return $shift->refresh();
        });
    }
}
