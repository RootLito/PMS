<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'order_no',
        'designation',
        'pap',
        'office',
        'office_pap',
    ];
}
