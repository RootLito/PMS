<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\RawCalculation;
use Livewire\Component;
use Carbon\Carbon;

use Livewire\WithPagination;

class RawComputation extends Component
{
    use WithPagination;
    public $remarks = '';
    public $cutoff = '';
    public $search = '';
    public $designation = '';
    public $selectedEmployee = null;
    public $monthly_rate = null;
    public $matchedRate;
    public $gross = null;
    public $daily = null;
    public $minutes = null;
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
    public $fields = [];



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



    // protected $deductionRates = [];

    // protected $deductionRates = [
    //     13378 => ['daily' => 608.09, 'halfday' => 304.04, 'hourly' => 76.01, 'per_min' => 1.26],
    //     15275 => ['daily' => 694.31, 'halfday' => 347.15, 'hourly' => 86.78, 'per_min' => 1.44],
    //     15368 => ['daily' => 698.54, 'halfday' => 349.27, 'hourly' => 87.31, 'per_min' => 1.45],
    //     15738 => ['daily' => 715.36, 'halfday' => 357.68, 'hourly' => 89.42, 'per_min' => 1.49],
    //     16458 => ['daily' => 748.09, 'halfday' => 374.04, 'hourly' => 93.51, 'per_min' => 1.55],
    //     16758 => ['daily' => 761.72, 'halfday' => 380.86, 'hourly' => 95.21, 'per_min' => 1.58],
    //     17179 => ['daily' => 780.86, 'halfday' => 390.43, 'hourly' => 97.60, 'per_min' => 1.62],
    //     17505 => ['daily' => 795.68, 'halfday' => 397.84, 'hourly' => 99.46, 'per_min' => 1.65],
    //     18251 => ['daily' => 829.59, 'halfday' => 414.79, 'hourly' => 103.69, 'per_min' => 1.72],
    //     18784 => ['daily' => 853.81, 'halfday' => 426.90, 'hourly' => 106.72, 'per_min' => 1.77],
    //     20572 => ['daily' => 935.09, 'halfday' => 467.54, 'hourly' => 116.88, 'per_min' => 1.94],
    //     21205 => ['daily' => 963.86, 'halfday' => 481.93, 'hourly' => 120.48, 'per_min' => 2.00],
    //     23877 => ['daily' => 1085.31, 'halfday' => 542.65, 'hourly' => 135.66, 'per_min' => 2.26],
    //     25232 => ['daily' => 1146.90, 'halfday' => 573.45, 'hourly' => 143.36, 'per_min' => 2.38],
    //     25439 => ['daily' => 1156.31, 'halfday' => 578.15, 'hourly' => 144.53, 'per_min' => 2.40],
    //     35097 => ['daily' => 1595.31, 'halfday' => 797.65, 'hourly' => 199.41, 'per_min' => 3.32],
    //     75359 => ['daily' => 3425.40, 'halfday' => 1712.70, 'hourly' => 428.17, 'per_min' => 7.13],
    // ];

    public function mount()
    {
        $day = Carbon::now()->day;
        if ($day >= 1 && $day <= 15) {
            $this->cutoff = '1-15';
        } elseif ($day >= 16 && $day <= 31) {
            $this->cutoff = '16-31';
        }
        $this->fields = $this->cutoffFields[$this->cutoff] ?? [];
        $this->currentCutoffLabel = $this->cutoffLabels[$this->cutoff] ?? '';
    }




    protected $cutoffLabels = [
        '1-15' => '1st Cutoff (1-15)',
        '16-31' => '2nd Cutoff (16-31)',
    ];
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

            $this->gross = $employee->gross;
            $this->net_late_absences = $employee->gross;
            $this->net_pay = $employee->gross;

            $this->monthly_rate = round((float) $employee->monthly_rate, 2);

            $this->matchedRate = $this->deductionRates[$this->monthly_rate] ?? null;

            if (!$this->matchedRate) {
                dd("No matched rate for: ", $this->monthly_rate, $this->deductionRates);
            }
        }
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
    }
    public function updatedDaily()
    {
        $this->calculateDailyAmount();
        $this->calculateDeduction();
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
                'daily' => round((float) $salary->daily_rate, 2),
                'halfday' => round((float) $salary->halfday_rate, 2),
                'hourly' => round((float) $salary->hourly_rate, 2),
                'per_min' => round((float) $salary->per_min_rate, 2),
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

                $this->calculateContributions();





                // $this->fetchDeductionRates();

                // Adjust net pay for tax and round
                $this->net_pay -= (float) $this->tax;
                $this->net_pay = round($this->net_pay, 2);
                if ($this->net_pay == 0) {
                    $this->net_pay = null; // Show empty if zero
                }

                // Add adjustment and round
                $this->net_pay += (float) $this->adjustment;
                $this->net_pay = round($this->net_pay, 2);
                if ($this->net_pay == 0) {
                    $this->net_pay = null; // Show empty if zero
                }
            }
        }
    }

    public function updated($propertyName)
    {
        $this->calculateDeduction();
    }
    protected function calculateContributions()
    {
        $contributions = null;
        foreach ($this->fields as $field) {
            $modelKey = $field['model'];
            $contributions += isset($this->{$modelKey}) ? (float) $this->{$modelKey} : null;
        }
        $this->net_pay = round($this->net_late_absences - $contributions, 2);
    }



    //SAVE RAW CALCULATION
    public function saveCalculation()
    {
        $totalDeduction =
            floatval($this->hdmf_pi) +
            floatval($this->hdmf_mpl) +
            floatval($this->hdmf_mp2) +
            floatval($this->hdmf_cl) +
            floatval($this->dareco) +
            floatval($this->ss_con) +
            floatval($this->ec_con) +
            floatval($this->wisp);

        $totalDeduction = ($totalDeduction == 0) ? null : $totalDeduction;

        $netLateAbsences = (float) $this->net_late_absences;
        $tax = (float) $this->tax;

        $this->net_tax = $netLateAbsences - $tax;

        RawCalculation::updateOrCreate(
            ['employee_id' => $this->selectedEmployee],
            [
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
                'remarks' => $this->remarks,
            ]
        );

        session()->flash('success', 'Calculation saved successfully.');
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
            ->paginate(10);



        return view('livewire.raw-computation', [
            'employees' => $employees
        ]);
    }
}
