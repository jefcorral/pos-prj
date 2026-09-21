<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    public static function log(
        string $action,
        ?Model $auditable = null,
        ?array $old = null,
        ?array $new = null,
    ): AuditLog {
        $user = auth()->user();
        $request = app('request');

        return AuditLog::create([
            'company_id' => $user?->company_id,
            'branch_id' => $user?->branch_id,
            'user_id' => $user?->id,
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }
}
