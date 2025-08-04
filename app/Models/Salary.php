<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'monthly_rate',
        'daily_rate',
        'halfday_rate',
        'hourly_rate',
        'per_min_rate'
    ];

}
