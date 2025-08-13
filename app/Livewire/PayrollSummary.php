<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Assigned;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;


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
    }
    public function proceed()
    {
        $this->showDesignations = false;
    }
    protected $cutoffFields = [
        '1-15' => [
            ['label' => 'HDMF-PI', 'model' => 'hdmf_pi'],
            ['label' => 'HDMF-MPL', 'model' => 'hdmf_mpl'],
            ['label' => 'HDMF-MP2', 'model' => 'hdmf_mp2'],
            ['label' => 'HDMF-CL', 'model' => 'hdmf_cl'],
            ['label' => 'DARECO', 'model' => 'dareco'],
        ],
        '16-31' => [
            ['label' => 'SS CON', 'model' => 'ss_con'],
            ['label' => 'EC CON', 'model' => 'ec_con'],
            ['label' => 'WISP', 'model' => 'wisp'],
            ['label' => '', 'model' => ''],
            ['label' => '', 'model' => ''],
        ],
    ];



    public $newDesignation = '';
    public $office_name = '';
    public $office_code = '';

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
    public $officeOptions = [
        'PFO DAVAO ORIENTAL' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'Driver' => '100000100001000',
        ],
        'Operation and Management of Production Facilities - TOS TAGABULI' => [
            'Operation and Management of Production Facilities - TOS TAGABULI' => '310102100002000',
            'Operation and Management of Production Facilities - TOS TAGABULI ' => '310102100001000',
        ],
        'PFO DAVAO DEL SUR' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'Driver' => '',
        ],
        'General Management and Supervision-PFO DAVAO DEL NORTE' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'PFO / MP-PANABO_IGACOS' => '310102100001000',
            'Driver' => '100000100001000',
        ],
        'TOS NABUNTURAN' => [
            'TOS - Broodstock Production and Maintenance' => '310102100001000',
            'TOS - Freshwater Fingerlings / Seed Production' => '310102100001000',
            'General Management and Supervision' => '100000100001000',
            'BASIL' => '',
            'WATCHMAN' => '',
        ],
        'PFO DAVAO DE ORO' => [
            'General Management and Supervision PFO - Mariculture' => '310300100001000',
            'FLDT' => '310300100001000',
            'Driver' => '100000100001000',
        ],
        'PFO DAVAO OCCIDENTAL' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'Driver' => '100000100001000',
            'MP Tubalan' => '310102100001000',
        ],
        'MULTI-SPECIES HATCHERY- BATO' => [
            'Multi-Species Hatchery' => '310102100002000',
            'Bay Management' => '310200100004000',
        ],
        'CFO DAVAO CITY' => [
            'General Management and Supervision' => '100000100001000',
            'FLDT' => '310300100001000',
        ],
        'SAAD' => [
            'PFO DAVAO OCCIDENTAL' => '310105200001000',
            'PFO DAVAO DEL SUR' => '310105200001000',
            'PFO DAVAO DEL NORTE' => '310105200001000',
            'PFO DAVAO ORIENTAL' => '310105200001000',
            'REGIONAL OFFICE' => '310105200001000',
            'SAAD-MAED' => '310105200001000',
        ],
        'FPSSD (LGU Assisted)' => [
            'FPSSD Norwegian Cage/Fish Pen/Mariculture Park' => '',
            'FPSSD PFO Davao Oriental' => '',
        ],
        'Monitoring, Control and Surveillance - FMRED' => [
            'ERMCSOC' => '310200100001000',
            'Driver' => '310200100001000',
            'Region' => '310200100001000',
            'CFVGL' => '310200100003000',
        ],
        "FISHERIES MANAGEMENT, REGULATORY AND ENFORCEMENT DIVISION (FMRED)" => [
            "IMEMS" => '310200100001000'
        ],
        'Research and Development - NSAP' => [
            'Research and Development - NSAP' => '200000100002',
        ],
        'Extension, Support, Education and Training Services (ESETS)' => [
            'Fisherfolk Coordination Unit' => '310300100001000',
            'Training Unit' => '310300100001000',
            'Information Unit' => '310300100001000',
        ],
        'Fisheries Laboratory Section' => [
            'HAB Monitoring' => '310200100002000',
            'Chem Lab' => '310200100001000',
            'Microbiology Lab' => '310200100002000',
        ],
        'General Management and Supervision - ORD' => [
            'Budget' => '100000100001000',
            'Accounting' => '100000100001000',
            'GSU' => '100000100001000',
            'Driver' => '100000100001000',
            'Admin' => '100000100001000',
            'Cashier' => '100000100001000',
            'BAC' => '100000100001000',
            'COA' => '100000100001000',
            'ORD' => '100000100001000',
            'GAD' => '100000100001000',
            'FSP' => '100000100001000',
            'GSU - Handyman' => '100000100001000',
            'HRMU' => '100000100001000',
        ],
        'Development of Organizational Policies, Plans & Procedures' => [
            'PMEU' => '200000100001000',
        ],
        'Fisheries Inspection and Quarantine Unit' => [
            'Fisheries Inspection and Quarantine Unit' => '310200100004000',
        ],
        'Regional Adjudication and Committee Secretariat' => [
            'Regional Adjudication and Committee Secretariat' => '200000100003000',
        ],
        'Regional Fisheries Information Management Unit - RFIMU' => [
            'Regional Fisheries Information Management Unit - RFIMU' => '',
        ],
        'FPSSD' => [
            'FPSSD - EPSDP' => '',
            "PHMS - Philippine Salt Industry Dev’t Project" => '',
        ],
    ];
    public function updatedDesignation($value)
    {
        $this->office_name = '';
        $this->office_code = '';
    }
    public function updatedOfficeName($value)
    {
        $this->office_code = $this->officeOptions[$this->newDesignation][$value] ?? '';
    }






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
            $employee->rawCalculation()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'voucher_include' => $this->newDesignation,
                    'office_name' => $this->office_name,
                    'office_code' => $this->office_code,
                ]
            );
        }
        $this->reset(['selectedEmployees', 'newDesignation', 'office_name', 'office_code']);
    }



    public function exportPayroll()
    {
        return Excel::download(new PayrollExport, 'payroll.xlsx');
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


        // $voucherNetPays = collect($this->designation)->mapWithKeys(function ($voucher) use ($filteredEmployees) {
        //     $totalNetPay = $filteredEmployees
        //         ->filter(function ($e) use ($voucher) {
        //             $effectiveDesignation = $e->rawCalculation->voucher_include ?? $e->designation;
        //             return $effectiveDesignation === $voucher;
        //         })
        //         ->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0));

        //     return [$voucher => $totalNetPay];
        // });

        // $groupedEmployees = $filteredEmployees
        //     ->groupBy(fn($e) => $e->rawCalculation->voucher_include ?? $e->designation)
        //     ->map(
        //         fn($group) =>
        //         $group->groupBy(
        //             fn($e) => $e->rawCalculation->office_name ?? $e->office_name
        //         )->map(fn($officeGroup) => [
        //                 'employees' => $officeGroup,
        //                 'office_code' => $officeGroup->first()->rawCalculation->office_code ?? $officeGroup->first()->office_code,
        //                 'totalGross' => $officeGroup->sum('gross'),
        //                 'totalAbsent' => $officeGroup->sum(fn($e) => $e->rawCalculation->absent ?? 0),
        //                 'totalLateUndertime' => $officeGroup->sum(fn($e) => $e->rawCalculation->late_undertime ?? 0),
        //                 'totalAbsentLate' => $officeGroup->sum(fn($e) => $e->rawCalculation->total_absent_late ?? 0),
        //                 'totalNetLateAbsences' => $officeGroup->sum(fn($e) => $e->rawCalculation->net_late_absences ?? 0),
        //                 'totalTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->tax ?? 0)),
        //                 'totalNetTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_tax ?? 0)),
        //                 'totalHdmfPi' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_pi ?? 0)),
        //                 'totalHdmfMpl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mpl ?? 0)),
        //                 'totalHdmfMp2' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mp2 ?? 0)),
        //                 'totalHdmfCl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_cl ?? 0)),
        //                 'totalDareco' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->dareco ?? 0)),
        //                 'totalSsCon' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->ss_con ?? 0)),
        //                 'totalEcCon' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->ec_con ?? 0)),
        //                 'totalWisp' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->wisp ?? 0)),
        //                 'totalTotalDeduction' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->total_deduction ?? 0)),
        //                 'totalNetPay' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0)),
        //             ])
        //     );



$groupedEmployees = $filteredEmployees
    ->groupBy(fn($e) => $e->rawCalculation->voucher_include ?? $e->designation)
    ->map(
        fn($group) =>
        $group->groupBy(
            fn($e) => $e->rawCalculation->office_name ?? $e->office_name
        )->map(fn($officeGroup) => [
                        'employees' => $officeGroup,
                        'office_code' => $officeGroup->first()->rawCalculation->office_code ?? $officeGroup->first()->office_code,
                        'office_name' => $officeGroup->first()->rawCalculation->office_name ?? $officeGroup->first()->office_name,
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



        $totalPerVoucher = $groupedEmployees->map(function ($offices) {
            return [
                'totalGross' => $offices->sum('totalGross'),
                'totalAbsent' => $offices->sum('totalAbsent'),
                'totalLateUndertime' => $offices->sum('totalLateUndertime'),
                'totalAbsentLate' => $offices->sum('totalAbsentLate'),
                'totalNetLateAbsences' => $offices->sum('totalNetLateAbsences'),
                'totalTax' => $offices->sum('totalTax'),
                'totalNetTax' => $offices->sum('totalNetTax'),
                'totalHdmfPi' => $offices->sum('totalHdmfPi'),
                'totalHdmfMpl' => $offices->sum('totalHdmfMpl'),
                'totalHdmfMp2' => $offices->sum('totalHdmfMp2'),
                'totalHdmfCl' => $offices->sum('totalHdmfCl'),
                'totalDareco' => $offices->sum('totalDareco'),
                'totalSsCon' => $offices->sum('totalSsCon'),
                'totalEcCon' => $offices->sum('totalEcCon'),
                'totalWisp' => $offices->sum('totalWisp'),
                'totalTotalDeduction' => $offices->sum('totalTotalDeduction'),
                'totalNetPay' => $offices->sum('totalNetPay'),
            ];
        });




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
        // $groupedVoucherEmployees = $filteredEmployees
        //     ->filter(fn($e) => !is_null($e->rawCalculation->voucher_include))
        //     ->groupBy(fn($e) => $e->rawCalculation->voucher_include)
        //     ->map(function ($voucherGroup) {
        //         return $voucherGroup->groupBy(fn($e) => $e->rawCalculation->office_name ?? $e->office_name)
        //             ->map(function ($officeGroup) {
        //                 return [
        //                     'employeeList' => $officeGroup,
        //                     'office_code' => $officeGroup->first()->rawCalculation->office_code ?? $officeGroup->first()->office_code,
        //                     'office_name' => $officeGroup->first()->rawCalculation->office_name ?? $officeGroup->first()->office_name,
        //                     'totalGross' => $officeGroup->sum('gross'),
        //                     'totalAbsent' => $officeGroup->sum(fn($e) => $e->rawCalculation->absent ?? 0),
        //                     'totalLateUndertime' => $officeGroup->sum(fn($e) => $e->rawCalculation->late_undertime ?? 0),
        //                     'totalAbsentLate' => $officeGroup->sum(fn($e) => $e->rawCalculation->total_absent_late ?? 0),
        //                     'totalNetLateAbsences' => $officeGroup->sum(fn($e) => $e->rawCalculation->net_late_absences ?? 0),
        //                     'totalTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->tax ?? 0)),
        //                     'totalNetTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_tax ?? 0)),
        //                     'totalHdmfPi' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_pi ?? 0)),
        //                     'totalHdmfMpl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mpl ?? 0)),
        //                     'totalHdmfMp2' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mp2 ?? 0)),
        //                     'totalHdmfCl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_cl ?? 0)),
        //                     'totalDareco' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->dareco ?? 0)),
        //                     'totalSsCon' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->ss_con ?? 0)),
        //                     'totalEcCon' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->ec_con ?? 0)),
        //                     'totalWisp' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->wisp ?? 0)),
        //                     'totalTotalDeduction' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->total_deduction ?? 0)),
        //                     'totalNetPay' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0)),
        //                 ];
        //             });
        //     });



        $cutoffFields = $this->cutoff === '1st'
            ? $this->cutoffFields['1-15']
            : ($this->cutoff === '2nd' ? $this->cutoffFields['16-31'] : []);


        return view(
            'livewire.payroll-summary',
            [
                'groupedEmployees' => $groupedEmployees,
                'cutoffFields' => $cutoffFields,
                // 'voucherNetPays' => $voucherNetPays,
                'overallTotal' => $overallTotal,
                'totalPerVoucher' => $totalPerVoucher
            ]
        );
    }
}
