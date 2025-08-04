<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class Voucher extends Component
{
    use WithPagination;

    public $month;
    public $year;
    public $cutoff;

    public $months = [
        '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
        '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December',
    ];

    public $years;

    

    public function mount()
    {
        $currentYear = date('Y');
        $this->years = range(2025, $currentYear + 5);

        // Set defaults to current month, year, and cutoff
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->month = date('m');
        $this->year = date('Y');

        $day = date('d');
        if ($day <= 15) {
            $this->cutoff = '1st';
        } else {
            $this->cutoff = '2nd';
        }
    }

    public function proceed()
    {
        // Just to trigger render with current filters
    }

    public function render()
    {
        $employeesQuery = Employee::with('rawCalculation')
            ->whereHas('rawCalculation', function ($q) {
                $q->where('is_completed', true);

                if ($this->month) {
                    $q->whereMonth('created_at', $this->month);
                }
                if ($this->year) {
                    $q->whereYear('created_at', $this->year);
                }
                if ($this->cutoff) {
                    if ($this->cutoff === '1st') {
                        $q->whereDay('created_at', '<=', 15);
                    } elseif ($this->cutoff === '2nd') {
                        $q->whereDay('created_at', '>=', 16);
                    }
                }
            });

        $employees = $employeesQuery->paginate(10);

        $designations = Employee::select('designation')->distinct()->pluck('designation');

        $netPayTotals = Employee::whereHas('rawCalculation', function ($q) {
            $q->where('is_completed', true);

            if ($this->month) {
                $q->whereMonth('created_at', $this->month);
            }
            if ($this->year) {
                $q->whereYear('created_at', $this->year);
            }
            if ($this->cutoff) {
                if ($this->cutoff === '1st') {
                    $q->whereDay('created_at', '<=', 15);
                } elseif ($this->cutoff === '2nd') {
                    $q->whereDay('created_at', '>=', 16);
                }
            }
        })
        ->with('rawCalculation')
        ->get()
        ->groupBy('designation')
        ->map(fn($group) => $group->sum(fn($employee) => $employee->rawCalculation->net_pay ?? 0));

        return view('livewire.voucher', [
            'employees' => $employees,
            'designations' => $designations,
            'netPayTotals' => $netPayTotals,
            'months' => $this->months,
            'years' => $this->years,
        ]);
    }
}
