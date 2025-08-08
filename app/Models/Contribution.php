<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = [
        'employee_id',
        'hdmf_pi',
        'hdmf_mpl',
        'hdmf_mp2',
        'hdmf_cl',
        'dareco',
        'sss',
        'ec',
        'wisp',
    ];

    protected $casts = [
        'hdmf_pi' => 'array',
        'hdmf_mpl' => 'array',
        'hdmf_mp2' => 'array',
        'hdmf_cl' => 'array',
        'dareco' => 'array',
        'sss' => 'array',
        'ec' => 'array',
        'wisp' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
