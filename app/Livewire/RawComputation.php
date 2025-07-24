<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Carbon\Carbon;

class RawComputation extends Component
{
    public $adjustments = '';
    public $remarks = '';
    public $SS_CON = '';
    public $EC_CON = '';
    public $WISP = '';
    public $cutoff = '';
    public $search = '';
    public $designation = '';
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
            'HDMF-PI',
            'HDMF-MPL',
            'HDMF-MP2',
            'HDMF-CL',
            'DARECO',
        ],
        '16-31' => [
            'SS CON',
            'EC CON',
            'WISP',
        ],
    ];


    public $selectedEmployee = null;
    public $monthly_rate = '';

    public $gross = '';
    public $daily = '';
    public $minutes = '';
    public $amount = '';
    public $adjustment_amount = '';
    public $total = '';
    public $net_late_absences = '';
    protected $deductionRates = [
        13378 => ['daily' => 608.09, 'halfday' => 304.04, 'hourly' => 76.01, 'per_min' => 1.26],
        15275 => ['daily' => 694.31, 'halfday' => 347.15, 'hourly' => 86.78, 'per_min' => 1.44],
        15368 => ['daily' => 698.54, 'halfday' => 349.27, 'hourly' => 87.31, 'per_min' => 1.45],
        15738 => ['daily' => 715.36, 'halfday' => 357.68, 'hourly' => 89.42, 'per_min' => 1.49],
        16458 => ['daily' => 748.09, 'halfday' => 374.04, 'hourly' => 93.51, 'per_min' => 1.55],
        16758 => ['daily' => 761.72, 'halfday' => 380.86, 'hourly' => 95.21, 'per_min' => 1.58],
        17179 => ['daily' => 780.86, 'halfday' => 390.43, 'hourly' => 97.60, 'per_min' => 1.62],
        17505 => ['daily' => 795.68, 'halfday' => 397.84, 'hourly' => 99.46, 'per_min' => 1.65],
        18251 => ['daily' => 829.59, 'halfday' => 414.79, 'hourly' => 103.69, 'per_min' => 1.72],
        18784 => ['daily' => 853.81, 'halfday' => 426.90, 'hourly' => 106.72, 'per_min' => 1.77],
        20572 => ['daily' => 935.09, 'halfday' => 467.54, 'hourly' => 116.88, 'per_min' => 1.94],
        21205 => ['daily' => 963.86, 'halfday' => 481.93, 'hourly' => 120.48, 'per_min' => 2.00],
        23877 => ['daily' => 1085.31, 'halfday' => 542.65, 'hourly' => 135.66, 'per_min' => 2.26],
        25232 => ['daily' => 1146.90, 'halfday' => 573.45, 'hourly' => 143.36, 'per_min' => 2.38],
        25439 => ['daily' => 1156.31, 'halfday' => 578.15, 'hourly' => 144.53, 'per_min' => 2.40],
        35097 => ['daily' => 1595.31, 'halfday' => 797.65, 'hourly' => 199.41, 'per_min' => 3.32],
        75359 => ['daily' => 3425.40, 'halfday' => 1712.70, 'hourly' => 428.17, 'per_min' => 7.13],
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
    }
    public function updatedCutoff($value)
    {
        $this->fields = $this->cutoffFields[$value] ?? [];
    }

    // $this->monthly_rate = $employee->monthly_rate; 


    // Handle employee selection and reset calculation values
    public function employeeSelected($employeeId)
    {
        $employee = Employee::find($employeeId);

        if ($employee) {
            $this->selectedEmployee = $employeeId;
            $this->gross = $employee->gross;


            $this->net_late_absences = $employee->gross;
        }
    }

    public function resetCalculation()
    {
        $this->daily = 0;
        $this->minutes = 0;
        $this->amount = 0;
        $this->adjustment_amount = 0;
        $this->total = 0;
        $this->net_late_absences = 0;
    }

    public function updatedDaily()
    {
        $this->calculateDeduction();
    }

    public function updatedMinutes()
    {
        $this->calculateDeduction();
    }

    public function calculateDeduction()
    {
        if ($this->selectedEmployee) {
            $employeeRate = $this->deductionRates[$this->selectedEmployee->monthly_rate] ?? null;

            if ($employeeRate) {
                $dailyDeduction = $this->daily * $employeeRate['daily'];
                $halfdayDeduction = ($this->daily == 0.5) ? $employeeRate['halfday'] : 0;
                $minutesDeduction = $this->minutes * $employeeRate['per_min'];

                $this->total = $dailyDeduction + $halfdayDeduction + $minutesDeduction;

                $this->net_late_absences = $this->gross - $this->total;
            }
        }
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
