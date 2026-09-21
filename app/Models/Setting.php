<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['company_id', 'key', 'value'];

    protected $casts = ['value' => 'array'];

    public static function get(string $key, mixed $default = null, ?int $companyId = null): mixed
    {
        $row = static::where('key', $key)
            ->where('company_id', $companyId)
            ->first();

        return $row?->value['value'] ?? $default;
    }

    public static function set(string $key, mixed $value, ?int $companyId = null): void
    {
        static::updateOrCreate(
            ['key' => $key, 'company_id' => $companyId],
            ['value' => ['value' => $value]]
        );
    }
}
