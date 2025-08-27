<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archived extends Model
{
    protected $table = 'archived';

    protected $fillable = [
        'filename',
        'cutoff',
        'date_saved',
    ];
}
