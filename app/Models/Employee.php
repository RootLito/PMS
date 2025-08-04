<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_initial',
        'suffix',
        'designation',
        'office_name',
        'office_code',
        'employment_status',
        'monthly_rate',
        'gross',
    ];

    public function rawCalculation()
    {
        return $this->hasOne(RawCalculation::class);
    }
}
