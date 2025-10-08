<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'last_name',
        'first_name',
        'middle_initial',
        'suffix',
        'designation',
        'designation_pap',
        'office_name',
        'office_code',
        'employment_status',
        'monthly_rate',
        'gross',
        'position',
        'gender',
    ];

    public function contribution()
    {
        return $this->hasOne(Contribution::class);
    }
    public function rawCalculation()
    {
        return $this->hasOne(RawCalculation::class);
    }
    public function rawCalculations()
    {
        return $this->hasMany(RawCalculation::class);
    }
}
