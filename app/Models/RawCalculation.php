<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawCalculation extends Model
{
    protected $fillable = [
        'is_completed',
        'employee_id',
        'absent',
        'late_undertime',
        'total_absent_late',
        'net_late_absences',
        'tax',
        'net_tax',
        'hdmf_pi',
        'hdmf_mpl',
        'hdmf_mp2',
        'hdmf_cl',
        'dareco',
        'ss_con',
        'ec_con',
        'wisp',
        'total_deduction',
        'net_pay',
        'remarks',
        'voucher_include',
        'office_code',
        'office_name',
        'cutoff',
        'month',       
        'year',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
