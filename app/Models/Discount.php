<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['company_id', 'name', 'type', 'value', 'is_active'];
}
