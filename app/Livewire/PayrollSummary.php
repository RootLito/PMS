<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Assigned;

class PayrollSummary extends Component
{
    protected $employees;
    public $cutoff = '';
    public $dateRange = '';
    public array $selectedEmployees = [];
    public string $selectedDesignation = '';
    public $designation = [];
    public $showDesignations = false;
    public $assigned;

    public function toggleDesignations()
    {
        $this->showDesignations = !$this->showDesignations;
        // $this->showDesignations = true;
    }
    public function proceed()
    {
        $this->showDesignations = false;
    }
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
            ['label' => '',       'model' => ''],
            ['label' => '',       'model' => ''],
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
        $today = Carbon::today();
        $day = $today->day;
        $year = $today->year;
        $month = strtoupper($today->format('F'));

        if ($day <= 15) {
            $this->cutoff = '1st';
            $this->dateRange = "{$month} 1-15, {$year}";
        } else {
            $this->cutoff = '2nd';
            $this->dateRange = "{$month} 16-31, {$year}";
        }


        $this->assigned = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    }
    public function updatedCutoff($value)
    {
        $today = Carbon::today();
        $year = $today->year;
        $month = strtoupper($today->format('F'));

        if ($value == '1st') {
            $this->dateRange = "{$month} 1-15, {$year}";
        } elseif ($value == '2nd') {
            $this->dateRange = "{$month} 16-31, {$year}";
        } else {
            $this->dateRange = '';
        }
    }
    public function confirmSelected()
    {
        $employees = Employee::with('rawCalculation')
            ->whereIn('id', $this->selectedEmployees)
            ->get();

        foreach ($employees as $employee) {
            if ($employee->rawCalculation) {
                $employee->rawCalculation->update([
                    'voucher_include' => $this->selectedDesignation,
                ]);
            }
        }

        $this->reset(['selectedEmployees', 'selectedDesignation']);
    }
    public function render()
    {
        $this->employees = Employee::with('rawCalculation')
            ->whereHas('rawCalculation', fn($q) => $q->where('is_completed', true))
            ->get();

        $filteredEmployees = $this->employees->filter(function ($employee) {
            $effectiveDesignation = $employee->rawCalculation->voucher_include ?? $employee->designation;

            return empty($this->designation)
                ? true
                : in_array($effectiveDesignation, $this->designation);
        });





        $voucherNetPays = collect($this->designation)->mapWithKeys(function ($voucher) use ($filteredEmployees) {
            $totalNetPay = $filteredEmployees
                ->filter(function ($e) use ($voucher) {
                    $effectiveDesignation = $e->rawCalculation->voucher_include ?? $e->designation;
                    return $effectiveDesignation === $voucher;
                })
                ->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0));

            return [$voucher => $totalNetPay];
        });


        $cutoffFields = $this->cutoff === '1st'
            ? $this->cutoffFields['1-15']
            : ($this->cutoff === '2nd' ? $this->cutoffFields['16-31'] : []);
        $groupedEmployees = $filteredEmployees
            ->groupBy(fn($e) => $e->rawCalculation->voucher_include ?? $e->designation)
            ->map(
                fn($group) =>
                $group->groupBy('office_name')
                    ->map(fn($officeGroup) => [
                        'employees' => $officeGroup,
                        'totalGross' => $officeGroup->sum('gross'),
                        'totalAbsent' => $officeGroup->sum(fn($e) => $e->rawCalculation->absent ?? 0),
                        'totalLateUndertime' => $officeGroup->sum(fn($e) => $e->rawCalculation->late_undertime ?? 0),
                        'totalAbsentLate' => $officeGroup->sum(fn($e) => $e->rawCalculation->total_absent_late ?? 0),
                        'totalNetLateAbsences' => $officeGroup->sum(fn($e) => $e->rawCalculation->net_late_absences ?? 0),
                        'totalTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->tax ?? 0)),
                        'totalNetTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_tax ?? 0)),
                        'totalHdmfPi' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_pi ?? 0)),
                        'totalHdmfMpl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mpl ?? 0)),
                        'totalHdmfMp2' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mp2 ?? 0)),
                        'totalHdmfCl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_cl ?? 0)),
                        'totalDareco' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->dareco ?? 0)),
                        'totalSsCon' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->ss_con ?? 0)),
                        'totalEcCon' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->ec_con ?? 0)),
                        'totalWisp' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->wisp ?? 0)),
                        'totalTotalDeduction' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->total_deduction ?? 0)),
                        'totalNetPay' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0)),
                    ])
            );
        $overallTotal = [
            'totalGross' => $filteredEmployees->sum('gross'),
            'totalAbsentLate' => $filteredEmployees->sum(fn($e) => $e->rawCalculation->total_absent_late ?? 0),
            'totalTax' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->tax ?? 0)),
            'totalHdmfPi' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->hdmf_pi ?? 0)),
            'totalHdmfMpl' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mpl ?? 0)),
            'totalHdmfMp2' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mp2 ?? 0)),
            'totalHdmfCl' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->hdmf_cl ?? 0)),
            'totalDareco' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->dareco ?? 0)),
            'totalSsCon' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->ss_con ?? 0)),
            'totalEcCon' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->ec_con ?? 0)),
            'totalWisp' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->wisp ?? 0)),
            'totalTotalDeduction' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->total_deduction ?? 0)),
            'totalNetPay' => $filteredEmployees->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0)),
        ];
        return view(
            'livewire.payroll-summary',
            [
                'groupedEmployees' => $groupedEmployees,
                'cutoffFields' => $cutoffFields,
                'voucherNetPays' => $voucherNetPays,
                'overallTotal' => $overallTotal,
            ]
        );
    }
}
