<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regular extends Model
{
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_initial',
        'suffix',
        'office',
        'position',
        'monthly_rate',
        'gross',
        'gender',
        'sl_code',
    ];
}
