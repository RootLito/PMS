<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'designation',
        'pap',
        'office',
        'office_pap',
    ];
}
