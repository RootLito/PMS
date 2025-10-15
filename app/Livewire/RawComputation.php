<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\RawCalculation;
use App\Models\Contribution;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Designation;

use Livewire\WithPagination;

class RawComputation extends Component
{
    use WithPagination;
    // public $remarks = '';
    public $cutoff = '';
    public $search = '';
    public $designation = '';
    public $sortOrder = '';
    public $employeeSelectedId = null;
    protected $queryString = ['employeeSelectedId' => ['as' => 'employee_id']];
    public $employeeName = '';
    public $selectedEmployee = null;
    public $monthly_rate = null;
    public $matchedRate;


    // gross  
    public $gross = null;
    public $baseGross = null;



    public $daily = null;
    public $minutes = null;
    public $absent = null;
    public $late = null;
    public $remarks2 = "";
    public $amount = null;
    public $min_amount = null;
    public $total = null;
    public $net_late_absences = null;
    public $adjustment = null;
    public $tax = null;
    public $net_tax = null;
    public $net_pay = null;
    public $ss_con = null;
    public $ec_con = null;
    public $wisp = null;
    public $hdmf_pi = null;
    public $hdmf_mpl = null;
    public $hdmf_mp2 = null;
    public $hdmf_cl = null;
    public $dareco = null;
    public $total_cont = null;
    public $currentCutoffLabel = '';
    public bool $showSaveModal = false;
    public $month;
    public $year;
    public $months = [];
    public $years = [];
    public $fields = [];
    public $mp2Entries = [];
    public $designations = [];
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
        ],
    ];
    protected $cutoffLabels = [
        '1-15' => '1st Cutoff (1-15)',
        '16-31' => '2nd Cutoff (16-31)',
    ];




    public function goToEmployeePage()
    {
        if (!$this->employeeSelectedId)
            return;
        $perPage = 10;
        $query = Employee::query();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->designation) {
            $query->where('designation', $this->designation);
        }
        if (in_array(strtolower($this->sortOrder), ['asc', 'desc'])) {
            $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
        }
        $allEmployeeIds = $query->pluck('id')->toArray();




        $index = array_search($this->employeeSelectedId, $allEmployeeIds);
        if ($index === false) {
            $this->dispatch('error', message: 'Employee not found in the current listing.');
            return;
        }
        $page = (int) ceil(($index + 1) / $perPage);
        $this->setPage($page);
    }
    public function mount()
    {
        $this->designations = Designation::pluck('designation')->unique()->sort()->values()->toArray();
        if ($this->employeeSelectedId) {
            $this->goToEmployeePage();
            $this->employeeSelected($this->employeeSelectedId);


        }
        $day = Carbon::now()->day;
        if ($day >= 1 && $day <= 15) {
            $this->cutoff = '1-15';
        } elseif ($day >= 16 && $day <= 31) {
            $this->cutoff = '16-31';
        }
        $this->fields = $this->cutoffFields[$this->cutoff] ?? [];
        $this->currentCutoffLabel = $this->cutoffLabels[$this->cutoff] ?? '';
        $this->initializeDateOptions();
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
    public function updatedCutoff($value)
    {
        $this->fields = $this->cutoffFields[$value] ?? [];
        $this->currentCutoffLabel = $this->cutoffLabels[$value] ?? '';
        foreach ($this->fields as $field) {
            if (!property_exists($this, $field['model'])) {
                $this->{$field['model']} = '';
            }
        }
    }
    public function employeeSelected($employeeId)
    {
        $this->deductionRates = [];
        $salaries = Salary::all();
        foreach ($salaries as $salary) {
            $monthlyRate = round((float) $salary->monthly_rate, 2);
            $this->deductionRates[$monthlyRate] = [
                'daily' => round((float) $salary->daily_rate, 2),
                'halfday' => round((float) $salary->halfday_rate, 2),
                'hourly' => round((float) $salary->hourly_rate, 2),
                'per_min' => round((float) $salary->per_min_rate, 2),
            ];
        }

        $employee = Employee::find($employeeId);

        if ($employee) {
            $this->resetCalculation();
            $this->selectedEmployee = $employeeId;


            // set gross 
            $this->gross = $employee->gross;
            $this->baseGross = $employee->gross;


            $this->net_late_absences = $employee->gross;
            $this->net_pay = $employee->gross;
            $this->employeeSelectedId = $employee->id;
            $this->employeeName = $employee->last_name . ', ' . $employee->first_name;
            if (!empty($employee->suffix)) {
                $this->employeeName .= ' ' . $employee->suffix;
            }
            if (!empty($employee->middle_initial)) {
                $this->employeeName .= ' ' . strtoupper(substr($employee->middle_initial, 0, 1)) . '.';
            }
            $this->monthly_rate = round((float) $employee->monthly_rate, 2);
            $this->matchedRate = $this->deductionRates[$this->monthly_rate] ?? null;
            if (!$this->matchedRate) {
                dd("No matched rate for: ", $this->monthly_rate, $this->deductionRates);
            }
        }

        //CONTRIBUTION
        $contribution = Contribution::where('employee_id', $employeeId)->first();
        if ($contribution) {
            $sss = json_decode($contribution->sss, true);
            $ec = json_decode($contribution->ec, true);
            $wisp = json_decode($contribution->wisp, true);
            $hdmf_pi = json_decode($contribution->hdmf_pi, true);
            $hdmf_mp2 = json_decode($contribution->hdmf_mp2, true);
            $hdmf_mpl = json_decode($contribution->hdmf_mpl, true);
            $hdmf_cl = json_decode($contribution->hdmf_cl, true);
            $dareco = json_decode($contribution->dareco, true);
            $taxData = json_decode($contribution->tax, true);
            $this->mp2Entries = json_decode($contribution->hdmf_mp2, true) ?? [];
            if (is_array($hdmf_mp2)) {
                $totalEeShare = array_sum(array_column($hdmf_mp2, 'ee_share'));
            } else {
                $totalEeShare = null;
            }

            // 1st cutoff 
            $this->hdmf_pi = $hdmf_pi['ee_share'] ?? null;
            $this->hdmf_mp2 = $totalEeShare ?? null;
            $this->hdmf_mpl = $hdmf_mpl['amount'] ?? null;
            $this->hdmf_cl = $hdmf_cl['cl_amount'] ?? null;
            $this->dareco = $dareco['amount'] ?? null;

            // 2nd cutoff 
            $this->ss_con = $sss['amount'] ?? null;
            $this->ec_con = $ec['amount'] ?? null;
            $this->wisp = $wisp['amount'] ?? null;

            $this->tax = floor(($taxData['tax'] ?? 0) / 2);
            if ($this->cutoff === '1-15') {
                $this->total_cont = (float) $this->hdmf_pi + (float) $this->hdmf_mp2 + (float) $this->hdmf_mpl
                    + (float) $this->hdmf_cl + (float) $this->dareco;
                $this->net_pay = $this->net_pay - $this->total_cont;
                $this->net_pay = $this->net_pay - $this->tax;
            } elseif ($this->cutoff === '16-31') {
                $this->total_cont = (float) $this->ss_con + (float) $this->ec_con + (float) $this->wisp;
                $this->net_pay = $this->net_pay - $this->total_cont;
                $this->net_pay = $this->net_pay - $this->tax;
            }


        } else {
            $this->resetContributionAmounts();
        }
    }
    public function resetContributionAmounts()
    {
        $this->ss_con = null;
        $this->ec_con = null;
        $this->wisp = null;
        $this->hdmf_pi = null;
        $this->hdmf_mp2 = null;
        $this->hdmf_mpl = null;
        $this->hdmf_cl = null;
        $this->dareco = null;
    }
    public function resetCalculation()
    {
        $this->daily = null;
        $this->minutes = null;
        $this->amount = null;
        $this->min_amount = null;
        $this->total = null;
        $this->net_late_absences = null;
        $this->net_pay = null;
        $this->ss_con = null;
        $this->ec_con = null;
        $this->wisp = null;
        $this->hdmf_pi = null;
        $this->hdmf_mpl = null;
        $this->hdmf_mp2 = null;
        $this->hdmf_cl = null;
        $this->dareco = null;
        $this->tax = null;
        $this->adjustment = null;
        $this->absent = null;
        $this->late = null;
        $this->remarks2 = '';
    }
    public function updatedDaily()
    {
        $this->calculateDailyAmount();
        $this->calculateDeduction();
        $this->absent = $this->daily;
    }
    public function updatedMinutes()
    {
        $this->calculateMinuteAmount();
        $this->calculateDeduction();
    }
    public function fetchDeductionRates()
    {
        $this->deductionRates = [];
        $salaries = Salary::all();
        foreach ($salaries as $salary) {
            $monthlyRate = round((float) $salary->monthly_rate, 2);
            $this->deductionRates[$monthlyRate] = [
                'daily' => floor((float) $salary->daily_rate * 100) / 100,
                'halfday' => floor((float) $salary->halfday_rate * 100) / 100,
                'hourly' => floor((float) $salary->hourly_rate * 100) / 100,
                'per_min' => floor((float) $salary->per_min_rate * 100) / 100,
            ];
        }
    }
    protected function getRate()
    {
        $this->fetchDeductionRates();
        $key = round((float) $this->monthly_rate, 2);
        return $this->deductionRates[$key] ?? null;
    }
    public function calculateDailyAmount()
    {
        $rate = $this->getRate();
        if ($rate) {
            $calculated = round((float) $this->daily * (float) $rate['daily'], 2);
            $this->amount = $calculated == 0 ? null : $calculated;
        } else {
            $this->amount = null;
        }
    }
    public function calculateMinuteAmount()
    {
        $rate = $this->getRate();
        if ($rate) {
            $calculated = round((float) $this->minutes * (float) $rate['per_min'], 2);
            $this->min_amount = $calculated == 0 ? null : $calculated;
        } else {
            $this->min_amount = null;
        }
    }


    public function calculateDeduction($applyLateAbsences = true)
    {
        if ($this->monthly_rate) {
            $employeeRate = $this->deductionRates[(int) $this->monthly_rate] ?? null;

            if ($employeeRate) {
                $daily = (float) $this->daily;
                $minutes = (float) $this->minutes;

                if ($applyLateAbsences) {
                    $dailyDeduction = ($daily == 0.5) ? $employeeRate['halfday'] : $daily * $employeeRate['daily'];
                    $minutesDeduction = $minutes * $employeeRate['per_min'];

                    $this->total = round($dailyDeduction + $minutesDeduction, 2);
                    if ($this->total == 0) {
                        $this->total = null;
                    }

                    $this->net_late_absences = $this->gross - $this->total;
                } else {
                    $this->total = null;
                    $this->net_late_absences = $this->gross;
                }
                $this->calculateNetPay();
            }
        }
    }


    // tax 
    public function updatedTax($value)
    {
        if (!is_null($value)) {
            $this->calculateTax();
        }
    }
    public function calculateTax()
    {
        $this->net_pay -= (float) $this->tax;
        $this->net_pay = round($this->net_pay, 2);

        // if ($this->net_pay == 0) {
        //     $this->net_pay = null;
        // }
    }



    // adjustment
    public function updatedAdjustment($value)
    {
        if (!is_null($value)) {
            $this->calculateAdjustment();
        }
    }

    public function calculateAdjustment()
    {
        // $this->net_pay += (float) $this->adjustment;
        // $this->net_pay = round($this->net_pay, 2);
        // if ($this->net_pay == 0) {
        //     $this->net_pay = null;
        // }
        $this->gross = $this->baseGross;
        $this->gross += (float) $this->adjustment;
        $this->gross = round($this->gross, 2);


        $this->net_late_absences = $this->gross;
    }




    // net pay
    // public function updatedNetPay($value)
    // {
    //     if (!is_null($value)) {
    //         $this->calculateNet();
    //     }
    // }
    // public function calculateNet(){
    //     $this->net_pay = $this->gross;
    // }




    // net pay 
    public function updated($propertyName)
    {
        $this->calculateDeduction();
        $this->calculateNetPay();
    }
    public function calculateNetPay()
    {
        $deductions = 0;
        foreach ($this->fields as $field) {
            $key = $field['model'];
            $deductions += isset($this->{$key}) ? (float) $this->{$key} : 0;
        }
        $this->net_pay = round($this->net_late_absences - $deductions, 2);
        if ($this->net_pay == 0) {
            $this->net_pay = null;
        }
    }



    public function saveCalculation()
    {
        $cutoff = $this->cutoff;

        if ($cutoff === '1-15') {
            $totalDeduction =
                floatval($this->hdmf_pi) +
                floatval($this->hdmf_mpl) +
                floatval($this->hdmf_mp2) +
                floatval($this->hdmf_cl) +
                floatval($this->dareco);
        } elseif ($cutoff === '16-31') {
            $totalDeduction =
                floatval($this->ss_con) +
                floatval($this->ec_con) +
                floatval($this->wisp);
        } else {
            $totalDeduction = 0;
        }

        $totalDeduction = ($totalDeduction == 0) ? null : $totalDeduction;

        $netLateAbsences = (float) $this->net_late_absences;
        $tax = (float) $this->tax;

        $this->net_tax = $netLateAbsences - $tax;

        $existing = RawCalculation::where('employee_id', $this->selectedEmployee)
            ->where('cutoff', $this->cutoff)
            ->first();

        $data = [
            'is_completed' => true,
            'absent' => $this->amount,
            'late_undertime' => $this->min_amount,
            'total_absent_late' => $this->total,
            'net_late_absences' => $this->net_late_absences,
            'tax' => $this->tax,
            'net_tax' => $this->net_tax,
            'hdmf_pi' => $this->hdmf_pi,
            'hdmf_mpl' => $this->hdmf_mpl,
            'hdmf_mp2' => $this->hdmf_mp2,
            'hdmf_cl' => $this->hdmf_cl,
            'dareco' => $this->dareco,
            'ss_con' => $this->ss_con,
            'ec_con' => $this->ec_con,
            'wisp' => $this->wisp,
            'total_deduction' => $totalDeduction,
            'net_pay' => $this->net_pay,
            'adjustment' => $this->adjustment,
            // 'remarks' => $this->remarks,
            'cutoff' => $this->cutoff,
            'month' => $this->month,
            'year' => $this->year,


            'absent_ins' => $this->absent,
            'late_ins' => $this->late,
            'remarks2' => $this->remarks2,
        ];

        if ($existing) {
            $existing->update($data);
        } else {
            RawCalculation::create(array_merge($data, [
                'employee_id' => $this->selectedEmployee,
            ]));
        }

        $this->dispatch('success', message: 'Payroll saved!');
        $this->resetCalculation();
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDesignation()
    {
        $this->resetPage();
    }
    public function render()
    {
        $employees = Employee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->designation, function ($query) {
                $query->where('designation', $this->designation);
            })
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            })
            ->paginate(12);
        return view('livewire.raw-computation', [
            'employees' => $employees
        ]);
    }
}
