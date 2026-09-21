<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['company_id', 'name', 'rate', 'type', 'is_active'];

    protected $casts = ['rate' => 'decimal:4', 'is_active' => 'boolean'];
}
