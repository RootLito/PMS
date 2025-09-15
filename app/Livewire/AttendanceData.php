<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Attendance;

class AttendanceData extends Component
{
    public $search = '';
    public $month = '';
    public $year = '';
    public $months = [];
    public $years = [];
    public $office = '';
    public $offices = [];

    public function mount()
    {
        $this->initializeDateOptions();
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }
    public function initializeDateOptions()
    {
        $this->months = collect(range(1, 12))->mapWithKeys(function ($monthNumber) {
            return [$monthNumber => Carbon::create()->month($monthNumber)->format('F')];
        })->toArray();

        $currentYear = Carbon::now()->year;
        $this->years = range($currentYear, $currentYear - 10);
    }
    public function prepareExport()
    {
        $employees = Employee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('middle_initial', 'like', '%' . $this->search . '%');
                });
            })
            ->with([
                'rawCalculations' => function ($query) {
                    $query->where('month', $this->month)
                        ->where('year', $this->year);
                }
            ])
            ->get();


        $this->offices = $employees->map(function ($employee) {
            return $employee->office_name ?: $employee->designation;
        })->unique()->sort()->values()->all();


        $groupedEmployees = [];
        foreach ($employees as $employee) {
            $designation = $employee->designation;
            $office = $employee->office_name ?: $designation;
            if ($this->office && $this->office !== $office) {
                continue;
            }


            if (!isset($groupedEmployees[$designation])) {
                $groupedEmployees[$designation] = [];
            }

            if (!isset($groupedEmployees[$designation][$office])) {
                $groupedEmployees[$designation][$office] = [];
            }

            $cutoffs = [
                '1st' => null,
                '2nd' => null
            ];

            foreach ($employee->rawCalculations as $calculation) {
                if ($calculation->cutoff === '1-15') {
                    $cutoffs['1st'] = $calculation;
                } elseif ($calculation->cutoff === '16-31') {
                    $cutoffs['2nd'] = $calculation;
                }
            }

            $absent_1 = $cutoffs['1st']->absent_ins ?? 0;
            $absent_2 = $cutoffs['2nd']->absent_ins ?? 0;
            $late_1 = $cutoffs['1st']->late_ins ?? 0;
            $late_2 = $cutoffs['2nd']->late_ins ?? 0;


            $groupedEmployees[$designation][$office][] = [
                'last_name' => $employee->last_name,
                'first_name' => $employee->first_name,
                'middle_initial' => $employee->middle_initial
                    ? strtoupper(substr($employee->middle_initial, 0, 1)) . '.'
                    : '',
                'monthly_rate' => $employee->monthly_rate,
                'absent_1' => $absent_1 ? $absent_1 : '',
                'absent_2' => $absent_2 ? $absent_2 : '',
                'absent_total' => ($absent_1 + $absent_2) ? ($absent_1 + $absent_2) : '',
                'late_1' => $late_1 ? $late_1 : '',
                'late_2' => $late_2 ? $late_2 : '',
                'late_total' => ($late_1 + $late_2) ? ($late_1 + $late_2) : '',
                'remarks' => trim(
                    ($cutoffs['1st']->remarks2 ?? '') .
                    ((isset($cutoffs['1st']->remarks2) && isset($cutoffs['2nd']->remarks2)) ? ', ' : '') .
                    ($cutoffs['2nd']->remarks2 ?? '')
                ),

            ];

        }
        return $groupedEmployees;
    }
    public function exportPayroll()
    {
        $exportData = $this->prepareExport();
        return Excel::download(
            new Attendance($exportData),
            'JOCOS ATTENDANCE ' . strtoupper(now()->format('F')) . ' ' . now()->year . '.xlsx'
        );
    }
    public function render()
    {
        $employees = Employee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('middle_initial', 'like', '%' . $this->search . '%');
                });
            })
            ->with([
                'rawCalculations' => function ($query) {
                    $query->where('month', $this->month)
                        ->where('year', $this->year);
                }
            ])
            ->get();
            
        $this->offices = $employees->map(function ($employee) {
            return $employee->office_name ?: $employee->designation;
        })->unique()->sort()->values()->all();

        $groupedEmployees = [];
        foreach ($employees as $employee) {
            $designation = $employee->designation;
            $office = $employee->office_name ?: $designation;
            if ($this->office && $this->office !== $office) {
                continue;
            }


            if (!isset($groupedEmployees[$designation])) {
                $groupedEmployees[$designation] = [];
            }

            if (!isset($groupedEmployees[$designation][$office])) {
                $groupedEmployees[$designation][$office] = [];
            }

            $cutoffs = [
                '1st' => null,
                '2nd' => null
            ];

            foreach ($employee->rawCalculations as $calculation) {
                if ($calculation->cutoff === '1-15') {
                    $cutoffs['1st'] = $calculation;
                } elseif ($calculation->cutoff === '16-31') {
                    $cutoffs['2nd'] = $calculation;
                }
            }

            $absent_1 = $cutoffs['1st']->absent_ins ?? 0;
            $absent_2 = $cutoffs['2nd']->absent_ins ?? 0;
            $late_1 = $cutoffs['1st']->late_ins ?? 0;
            $late_2 = $cutoffs['2nd']->late_ins ?? 0;

            $groupedEmployees[$designation][$office][] = [
                'last_name' => $employee->last_name,
                'first_name' => $employee->first_name,
                'middle_initial' => $employee->middle_initial,
                'monthly_rate' => $employee->monthly_rate,
                'absent_1' => $absent_1,
                'absent_2' => $absent_2,
                'absent_total' => $absent_1 + $absent_2,
                'late_1' => $late_1,
                'late_2' => $late_2,
                'late_total' => $late_1 + $late_2,
                'remarks' => trim(
                    ($cutoffs['1st']->remarks2 ?? '') .
                    ((isset($cutoffs['1st']->remarks2) && isset($cutoffs['2nd']->remarks2)) ? ', ' : '') .
                    ($cutoffs['2nd']->remarks2 ?? '')
                ),
            ];
        }

        return view('livewire.attendance-data', [
            'groupedEmployees' => $groupedEmployees,
        ]);
    }
}
