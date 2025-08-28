<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Designation;
use App\Models\RawCalculation;
use App\Models\Assigned;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use Illuminate\Support\Facades\Storage;
use App\Models\Archived;




class PayrollSummary extends Component
{
    protected $employees;
    public $cutoff = '';
    public $dateRange = '';
    public string $selectedDesignation = '';
    public $showDesignations = false;
    public $assigned;
    public $newDesignation = '';
    public $office_name = '';
    public $office_code = '';
    public array $selectedEmployees = [];
    public $designation = [];
    public $designations = [];
    public $officeOptions = [];

    public $month;
    public $year;

    public $months = [];
    public $years = [];
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
    public function updatedDesignation($value)
    {
        $this->office_name = '';
        $this->office_code = '';
    }
    public function updatedOfficeName($value)
    {
        $this->office_code = $this->officeOptions[$this->newDesignation][$value] ?? '';
    }
    // public function mount()
    // {
    //     $today = Carbon::today();
    //     $day = $today->day;
    //     $year = $today->year;
    //     $month = strtoupper($today->format('F'));
    //     if ($day <= 15) {
    //         $this->cutoff = '1-15';
    //         $this->dateRange = "{$month} 1-15, {$year}";
    //     } else {
    //         $this->cutoff = '16-31';
    //         $this->dateRange = "{$month} 16-31, {$year}";
    //     }
    //     $designationsData = Designation::all();
    //     $this->designations = $designationsData
    //         ->pluck('designation')
    //         ->unique()
    //         ->values()
    //         ->toArray();

    //     foreach ($designationsData as $item) {
    //         $this->officeOptions[$item->designation][$item->office] = $item->pap;
    //     }
    //     $this->assigned = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    //     $this->initializeDateOptions();
    // }



    public function mount()
    {
        // Use the class properties $month and $year instead of re-initializing date here
        // For example, if they aren't set yet, set defaults (current month and year)
        $this->month = $this->month ?? strtoupper(Carbon::now()->format('F'));
        $this->year = $this->year ?? Carbon::now()->year;

        // Determine today's day to assign cutoff and dateRange
        $todayDay = Carbon::now()->day;

        if ($todayDay <= 15) {
            $this->cutoff = '1-15';
            $this->dateRange = "{$this->month} 1-15, {$this->year}";
        } else {
            $this->cutoff = '16-31';
            $this->dateRange = "{$this->month} 16-31, {$this->year}";
        }

        // Other initializations remain the same
        $designationsData = Designation::all();
        $this->designations = $designationsData
            ->pluck('designation')
            ->unique()
            ->values()
            ->toArray();

        foreach ($designationsData as $item) {
            $this->officeOptions[$item->designation][$item->office] = $item->pap;
        }

        $this->assigned = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();

        $this->initializeDateOptions();
    }






    public function updatedMonth($value)
    {
        $this->updateCutoffAndDateRange();
    }

    public function updatedYear($value)
    {
        $this->updateCutoffAndDateRange();
    }

    protected function updateCutoffAndDateRange()
    {
        $todayDay = Carbon::now()->day;

        if ($todayDay <= 15) {
            $this->cutoff = '1-15';
            $this->dateRange = strtoupper(Carbon::createFromDate($this->year, $this->month)->format('F')) . " 1-15, {$this->year}";
        } else {
            $this->cutoff = '16-31';
            $this->dateRange = strtoupper(Carbon::createFromDate($this->year, $this->month)->format('F')) . " 16-31, {$this->year}";
        }
    }
    public function updatedCutoff($value)
    {
        $monthName = $this->month
            ? strtoupper(Carbon::createFromDate($this->year, $this->month)->format('F'))
            : '';

        $year = $this->year ?? Carbon::now()->year;

        if ($value == '1-15') {
            $this->dateRange = "{$monthName} 1-15, {$year}";
        } elseif ($value == '16-31') {
            $this->dateRange = "{$monthName} 16-31, {$year}";
        } else {
            $this->dateRange = '';
        }
    }











    public function initializeDateOptions()
    {
        $this->months = collect(range(1, 12))->mapWithKeys(function ($monthNumber) {
            return [$monthNumber => Carbon::create()->month($monthNumber)->format('F')];
        })->toArray();

        $currentYear = Carbon::now()->year;

        $this->years = range($currentYear, $currentYear - 10);

        $this->month = Carbon::now()->month;
        $this->year = $currentYear;
    }





    //SAVE----------------------------------------
    public function confirmSelected()
    {
        if (empty($this->newDesignation)) {
            $this->dispatch('error', message: 'Please select a new designation before transferring.');
            return;
        }

        $employees = Employee::with('rawCalculation')
            ->whereIn('id', $this->selectedEmployees)
            ->get();

        foreach ($employees as $employee) {
            $employee->rawCalculation()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'voucher_include' => $this->newDesignation,
                ]
            );
        }
        $this->dispatch('success', message: 'Employee transferred!');
        $this->reset(['selectedEmployees', 'newDesignation', 'office_name', 'office_code']);
    }
    //DELETE--------------------------------------
    public function deleteSelected()
    {
        if (empty($this->selectedEmployees)) {
            $this->dispatch('error', message: 'No employees selected for deletion.');
            return;
        }
        $deleted = RawCalculation::whereIn('employee_id', $this->selectedEmployees)
            ->where('cutoff', $this->cutoff)
            ->delete();

        if ($deleted) {
            $this->dispatch('success', message: 'Computation data removed from payroll.');
        } else {
            $this->dispatch('error', message: 'No computation data found for selected employees.');
        }
        $this->reset(['selectedEmployees']);
    }
    //UPDATE--------------------------------------
    public function redirectToEdit()
    {
        if (count($this->selectedEmployees) !== 1) {
            $this->dispatch('error', message: 'Please select only one employee to update.');
            return;
        }
        $employeeId = $this->selectedEmployees[0];
        return redirect()->to('/computation?employee_id=' . $employeeId);
    }



    //EXPORT DATA---------------------------------
    private function prepareExportData()
    {
        $this->employees = Employee::with([
            'rawCalculations' => function ($query) {
                $query->where('is_completed', true);
            }
        ])
            ->whereHas('rawCalculations', fn($q) => $q->where('is_completed', true))
            ->get();

        $filteredEmployees = $this->employees->filter(function ($employee) {
            $rawCalculation = $employee->rawCalculations->firstWhere('cutoff', $this->cutoff);
            $effectiveDesignation = $rawCalculation?->voucher_include ?? $employee->designation;
            $designationMatch = empty($this->designation) || in_array($effectiveDesignation, $this->designation);

            $cutoffMatch = empty($this->cutoff) || ($rawCalculation?->cutoff === $this->cutoff);

            return $designationMatch && $cutoffMatch;
        });

        $groupedEmployees = $filteredEmployees->groupBy(fn($e) => $e->rawCalculation->voucher_include ?? $e->designation)
            ->map(fn($group) => $group->groupBy(fn($e) => $e->rawCalculation->office_name ?? $e->office_name)
                ->map(fn($officeGroup) => [
                    'employees' => $officeGroup,
                    'office_code' => $officeGroup->first()->rawCalculation->office_code ?? $officeGroup->first()->office_code,
                    'office_name' => $officeGroup->first()->rawCalculation->office_name ?? $officeGroup->first()->office_name,
                    'totalGross' => $officeGroup->sum('gross'),
                    'totalNetLateAbsences' => $officeGroup->sum(fn($e) => $e->rawCalculation->net_late_absences ?? 0),
                    'totalNetTax' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_tax ?? 0)),
                    'totalHdmfPi' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_pi ?? 0)),
                    'totalHdmfMpl' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->hdmf_mpl ?? 0)),
                    'totalTotalDeduction' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->total_deduction ?? 0)),
                    'totalNetPay' => $officeGroup->sum(fn($e) => (float) ($e->rawCalculation->net_pay ?? 0)),
                ]));

        $totalPerVoucher = $groupedEmployees->map(fn($offices) => [
            'totalGross' => $offices->sum('totalGross'),
            'totalNetLateAbsences' => $offices->sum('totalNetLateAbsences'),
            'totalNetTax' => $offices->sum('totalNetTax'),
            'totalHdmfPi' => $offices->sum('totalHdmfPi'),
            'totalHdmfMpl' => $offices->sum('totalHdmfMpl'),
            'totalTotalDeduction' => $offices->sum('totalTotalDeduction'),
            'totalNetPay' => $offices->sum('totalNetPay'),
        ]);



        $cutoffFields = $this->cutoff === '1-15'
            ? $this->cutoffFields['1-15']
            : ($this->cutoff === '16-31' ? $this->cutoffFields['16-31'] : []);




        return [
            'groupedEmployees' => $groupedEmployees,
            'totalPerVoucher' => $totalPerVoucher,
            'cutoffFields' => $cutoffFields,
            'dateRange' => $this->dateRange,
            'assigned' => $this->assigned,
            'cutoff' => $this->cutoff,
        ];
    }


    //ARCHIVE-------------------------------------
    public function saveArchive()
    {
        $exportData = $this->prepareExportData();
        $filename = 'COS Payroll ' . now()->year . ' - Region XI_' . now()->format('Ymd_His') . '.xlsx';
        $path = 'archives/' . $filename;
        Excel::store(new PayrollExport($exportData), $path, 'public');
        Archived::create([
            'filename' => $filename,
            'cutoff' => $this->cutoff,
            'date_saved' => now(),
        ]);
        $this->dispatch('success', message: 'Archive saved successfully!');
    }



    //EXPORT --------------------------------------
    public function exportPayroll()
    {
        $exportData = $this->prepareExportData();





        
        return Excel::download(new PayrollExport($exportData), 'payroll.xlsx');
    }






    public function render()
    {
        $this->employees = Employee::with([
            'rawCalculations' => function ($q) {
                $q->where('is_completed', true)
                    ->where('month', $this->month)
                    ->where('year', $this->year);
            }
        ])->get();

        $filteredEmployees = collect();

        foreach ($this->employees as $employee) {
            foreach ($employee->rawCalculations as $calculation) {
                $cutoffMatch = empty($this->cutoff) || $calculation->cutoff === $this->cutoff;
                $effectiveDesignation = $calculation->voucher_include ?? $employee->designation;

                $designationMatch = empty($this->designation) || in_array($effectiveDesignation, $this->designation);

                if ($cutoffMatch && $designationMatch) {
                    $empClone = clone $employee;
                    $empClone->rawCalculation = $calculation;
                    $filteredEmployees->push($empClone);
                }
            }
        }

        $groupedEmployees = $filteredEmployees
            ->groupBy(fn($e) => $e->rawCalculation->voucher_include ?? $e->designation)
            ->map(function ($group) {
                $designationPap = $group->first()->rawCalculation->designation_pap ?? $group->first()->designation_pap ?? null;

                $offices = $group
                    ->groupBy(fn($e) => $e->rawCalculation->office_name ?? $e->office_name)
                    // ->groupBy(fn($e) => strtolower(trim($e->rawCalculation->office_name ?? $e->office_name)))
    
                    ->map(fn($officeGroup) => [
                        'employees' => $officeGroup,
                        'office_code' => $officeGroup->first()->rawCalculation->office_code ?? $officeGroup->first()->office_code ?? null,
                        'office_name' => $officeGroup->first()->rawCalculation->office_name ?? $officeGroup->first()->office_name ?? null,
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
                    ]);

                return [
                    'designation_pap' => $designationPap,
                    'offices' => $offices,
                ];
            });

        $totalPerVoucher = $groupedEmployees->map(function ($group) {
            $offices = collect($group['offices']);
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

        $cutoffFields = $this->cutoff === '1-15'
            ? $this->cutoffFields['1-15']
            : ($this->cutoff === '16-31' ? $this->cutoffFields['16-31'] : []);

        return view('livewire.payroll-summary', [
            'groupedEmployees' => $groupedEmployees,
            'cutoffFields' => $cutoffFields,
            'overallTotal' => $overallTotal,
            'totalPerVoucher' => $totalPerVoucher,
        ]);
    }
}
