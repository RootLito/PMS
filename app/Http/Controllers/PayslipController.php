<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Assigned;

class PayslipController extends Controller
{


    public function printPayslip(Request $request, $employeeId)
    {
        $employee = Employee::with(['contribution', 'rawCalculation', 'rawCalculations'])->findOrFail($employeeId);
        $assigned = Assigned::with(['noted'])->latest()->first();

        $notedByName = $assigned?->noted?->name ?? 'N/A';
        $notedByDesignation = $assigned?->noted?->designation ?? 'N/A';

        $fullName = strtoupper(trim(
            $employee->first_name . ' ' .
            ($employee->middle_initial ? substr($employee->middle_initial, 0, 1) . '. ' : '') .
            $employee->last_name .
            ($employee->suffix ? ' ' . $employee->suffix . '.' : '')
        ));

        $mp2Data = optional($employee->contribution)->hdmf_mp2;
        $mp2Total = null;

        if ($mp2Data) {
            $mp2Array = json_decode($mp2Data, true);
            if (is_array($mp2Array)) {
                $mp2Total = array_sum(array_column($mp2Array, 'ee_share'));
            }
        }

        $contributions = [
            'hdmf_pi' => optional(json_decode($employee->contribution->hdmf_pi ?? '{}', true))['ee_share'] ?? null,
            'hdmf_mpl' => optional(json_decode($employee->contribution->hdmf_mpl ?? '{}', true))['amount'] ?? null,
            'hdmf_mp2' => $mp2Total,
            'hdmf_cl' => optional(json_decode($employee->contribution->hdmf_cl ?? '{}', true))['cl_amount'] ?? null,
            'dareco' => optional(json_decode($employee->contribution->dareco ?? '{}', true))['dareco_amount'] ?? null,
            'sss' => optional(json_decode($employee->contribution->sss ?? '{}', true))['amount'] ?? null,
            'ec' => optional(json_decode($employee->contribution->ec ?? '{}', true))['amount'] ?? null,
            'wisp' => optional(json_decode($employee->contribution->wisp ?? '{}', true))['amount'] ?? null,
        ];

        // dd($contributions);

        $total_absent_late = $employee->rawCalculations->sum(fn($c) => floatval($c->total_absent_late ?? 0));
        $tax = optional($employee->rawCalculation)->tax ?? 0;

        $total_deductions = array_sum(array_map(fn($v) => $v ?? 0, $contributions)) + $total_absent_late + $tax;
        $net_pay = $employee->monthly_rate - $total_deductions;

        $rawDate = $request->input('date') ?? now();
        $carbonDate = Carbon::parse($rawDate);

        $formattedDate = $carbonDate->format('jS') . ' day of ' . $carbonDate->format('F Y');
        $rawDateForInput = $carbonDate->format('Y-m-d');
        $coveragePeriod = $carbonDate->format('F') . ' 1–' . $carbonDate->endOfMonth()->format('j, Y');

        $ftmRaw = $request->input('ftm') ?? now();
        $ftmDate = Carbon::parse($ftmRaw);
        $ftmCoverage = $ftmDate->format('F') . ' 1–' . $ftmDate->endOfMonth()->format('j, Y');


        $controlNumber = $request->input('control_number');


        return view('jocos.payslip', [
            'full_name' => $fullName,
            'position' => $employee->position,
            'gross_monthly_income' => $employee->monthly_rate,
            'contributions' => $contributions,
            'total_absent_late' => $total_absent_late,
            'tax' => $tax,
            'total_deductions' => $total_deductions,
            'net_pay' => $net_pay,
            'issued_date' => $formattedDate,
            'raw_date' => $rawDateForInput,
            'employeeId' => $employee->id,
            'coverage_period' => $coveragePeriod,
            'noted_by_name' => $notedByName,
            'noted_by_designation' => $notedByDesignation,
            'controlNumber' => $controlNumber,
            'ftm_coverage' => $ftmCoverage,
            'ftm_raw' => $ftmDate->format('Y-m-d'),

        ]);
    }

}
