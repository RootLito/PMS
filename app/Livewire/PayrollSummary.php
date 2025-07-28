<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class PayrollSummary extends Component
{
    protected $employees;

    public $cutoff = '';
    protected $cutoffFields = [
        '1-15' => [
            ['label' => 'HDMF-PI',  'model' => 'hdmf_pi'],
            ['label' => 'HDMF-MPL', 'model' => 'hdmf_mpl'],
            ['label' => 'HDMF-MP2', 'model' => 'hdmf_mp2'],
            ['label' => 'HDMF-CL',  'model' => 'hdmf_cl'],
            ['label' => 'DARECO',   'model' => 'dareco'],
        ],
        '16-31' => [
            ['label' => 'SS CON',   'model' => 'ss_con'],
            ['label' => 'EC CON',   'model' => 'ec_con'],
            ['label' => 'WISP',     'model' => 'wisp'],
        ],
    ];


    public $designations = [
        "CFO DAVAO CITY",
        "Development of Organizational Policies, Plans & Procedures",
        "Extension, Support, Education and Training Services (ESETS)",
        "Fisheries Inspection and Quarantine Unit",
        "Fisheries Laboratory Section",
        "FPSSD",
        "FPSSD (LGU Assisted)",
        "General Management and Supervision - ORD",
        "General Management and Supervision-PFO DAVAO DEL NORTE",
        "Monitoring, Control and Surveillance - FMRED",
        "MULTI-SPECIES HATCHERY- BATO",
        "Operation and Management of Production Facilities - TOS TAGABULI",
        "PFO DAVAO DE ORO",
        "PFO DAVAO DEL SUR",
        "PFO DAVAO OCCIDENTAL",
        "PFO DAVAO ORIENTAL",
        "Regional Adjudication and Committee Secretariat",
        "Regional Fisheries Information Management Unit - RFIMU",
        "SAAD",
        "TOS NABUNTURAN"
    ];




    public function mount()
    {
        $this->employees = Employee::with('rawCalculation')
            ->whereHas('rawCalculation', fn($q) => $q->where('is_completed', true))
            ->get();

        // Log::info('Employees fetched: ' . $this->employees->count());
    }

    public function render()
    {
        $groupedEmployees = $this->employees
            ->groupBy('designation')
            ->map(
                fn($group) => $group->groupBy('office_name')
                    ->map(fn($officeGroup) => [
                        'employees' => $officeGroup,
                        'totalGross' => $officeGroup->sum('gross'),
                        'totalAbsent' => $officeGroup->sum(fn($employee) => $employee->rawCalculation->absent ?? 0),
                        'totalLateUndertime' => $officeGroup->sum(fn($employee) => $employee->rawCalculation->late_undertime ?? 0),
                        'totalAbsentLate' => $officeGroup->sum(fn($employee) => $employee->rawCalculation->total_absent_late ?? 0),
                        'totalNetLateAbsences' => $officeGroup->sum(fn($employee) => $employee->rawCalculation->net_late_absences ?? 0),
                        'totalTax' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->tax ?? 0),
                        'totalNetTax' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->net_tax ?? 0),
                        'totalHdmfPi' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->hdmf_pi ?? 0),
                        'totalHdmfMpl' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->hdmf_mpl ?? 0),
                        'totalHdmfMp2' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->hdmf_mp2 ?? 0),
                        'totalHdmfCl' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->hdmf_cl ?? 0),
                        'totalDareco' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->dareco ?? 0),
                        'totalSsCon' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->ss_con ?? 0),
                        'totalEcCon' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->ec_con ?? 0),
                        'totalWisp' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->wisp ?? 0),
                        'totalTotalDeduction' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->total_deduction ?? 0),
                        'totalNetPay' => $officeGroup->sum(fn($employee) => (float) $employee->rawCalculation->net_pay ?? 0),
                    ])
            );

        return view('livewire.payroll-summary', compact('groupedEmployees'));
    }
}
