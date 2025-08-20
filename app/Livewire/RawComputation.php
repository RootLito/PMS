<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\RawCalculation;
use App\Models\Contribution;
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
    public $sortOrder = '';
    public $employeeSelectedId = null;
    public $employeeName = '';
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
    public bool $showSaveModal = false;

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

    public $mp2Entries = [];
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

            $this->mp2Entries = json_decode($contribution->hdmf_mp2, true) ?? [];
            // $eeShares = array_column($hdmf_mp2, 'ee_share'); 
            // $totalEeShare = array_sum(array_column($hdmf_mp2, 'ee_share'));
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

            if ($this->cutoff === '1-15') {
                $this->total_cont = (float) $this->hdmf_pi + (float) $this->hdmf_mp2 + (float) $this->hdmf_mpl
                    + (float) $this->hdmf_cl + (float) $this->dareco;
                $this->net_pay = $this->net_pay - $this->total_cont;
            } elseif ($this->cutoff === '16-31') {
                $this->total_cont = (float) $this->ss_con + (float) $this->ec_con + (float) $this->wisp;
                $this->net_pay = $this->net_pay - $this->total_cont;
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

                $this->calculateNetPay();


            }
        }
    }

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

        if ($this->net_pay == 0) {
            $this->net_pay = null;
        }
    }


    public function updatedAdjustment($value)
    {
        if (!is_null($value)) {
            $this->calculateAdjustment();
        }
    }

    public function calculateAdjustment()
    {
        $this->net_pay += (float) $this->adjustment;
        $this->net_pay = round($this->net_pay, 2);
        if ($this->net_pay == 0) {
            $this->net_pay = null;
        }
    }




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
        $this->dispatch('success', message: 'Payroll added!');
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
            ->paginate(10);



        return view('livewire.raw-computation', [
            'employees' => $employees
        ]);
    }
}
