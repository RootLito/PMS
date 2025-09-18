<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contribution;
use App\Models\Employee;
use Carbon\Carbon;
use Livewire\WithPagination;

class DashboardData extends Component
{
    use WithPagination;
    public $reminderData = [];
    public $joCount = 0;
    public $nsap = 0;
    public $cosCount = 0;
    public $totalCount = 0;
    public $maleCount = 0;
    public $femaleCount = 0;
    public $showModal = false;
    public $totalPi = 0;
    public $totalMp2 = 0;
    public $totalMpl = 0;
    public $totalCl = 0;
    public $totalDareco = 0;
    public $totalSssEcWisp = 0;
    public $month = '';
    public $attMonth = '';
    public $year = '';
    public $employeesData = [];
    public $date;
    public $search = '';
    public $office = '';
    public $offices = [];

    public function mount()
    {
        $this->loadReminderData();
        $this->loadEmployeesData();

        $this->nsap = Employee::where('designation', 'RESEARCH AND DEVELOPMENT - NSAP')->count();

        $this->joCount = Employee::where('employment_status', 'JO')
            ->where('designation', '!=', 'RESEARCH AND DEVELOPMENT - NSAP')
            ->count();

        $this->cosCount = Employee::where('employment_status', 'COS')
            ->where('designation', '!=', 'RESEARCH AND DEVELOPMENT - NSAP')
            ->count();

        $this->totalCount = $this->joCount + $this->cosCount;

        $this->maleCount = Employee::where('gender', 'male')->count();
        $this->femaleCount = Employee::where('gender', 'female')->count();

        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;

        $this->attMonth = $this->attMonth = Carbon::now()->format('F');
        $this->date = Carbon::now()->format('m/d/Y');
    }
    public function loadReminderData()
    {
        $this->reminderData = [];
        $contributions = Contribution::all();
        $tomorrow = Carbon::tomorrow();

        foreach ($contributions as $contribution) {
            if ($contribution->hdmf_mpl) {
                $jsonString = stripslashes($contribution->hdmf_mpl);
                $data = json_decode($jsonString, true);

                if (isset($data['end_te'])) {
                    $endTermDate = Carbon::parse($data['end_te']);

                    if ($endTermDate->isSameDay($tomorrow)) {
                        $employee = Employee::find($contribution->employee_id);

                        if ($employee) {
                            $middle = $employee->middle_initial
                                ? strtoupper(substr($employee->middle_initial, 0, 1)) . '.'
                                : '';
                            $suffix = $employee->suffix ? $employee->suffix . '.' : '';
                            $fullName = trim("{$employee->first_name} {$middle} {$employee->last_name} {$suffix}");

                            $this->reminderData[] = [
                                'contribution_id' => $contribution->id,
                                'employee_id' => $employee->id,
                                'full_name' => $fullName,
                                'end_term_date' => $endTermDate->format('d/m/Y')
                            ];
                        }
                    }
                }
            }
        }

        $this->showModal = count($this->reminderData) > 0;
    }
    public function loadEmployeesData()
    {
        $this->employeesData = Employee::whereHas('rawCalculations', function ($query) {
            $query->where(function ($q) {
                $q->where('absent_ins', '>=', 9)
                    ->orWhere('late_ins', '>=', 9);
            });
        })
            ->withSum('rawCalculations as total_absent_ins', 'absent_ins')
            ->withSum('rawCalculations as total_late_ins', 'late_ins')
            ->paginate(5, ['id', 'first_name', 'last_name', 'middle_initial']);


        $result = $this->employeesData->filter(function ($employee) {
            return $employee->total_absent_ins >= 9 || $employee->total_late_ins >= 9;
        })->map(function ($employee) {
            $getStatus = function ($count) {
                if ($count > 9) {
                    return 'memo';
                } elseif ($count == 9) {
                    return 'warning';
                } else {
                    return null;
                }
            };

            return [
                'full_name' => trim("{$employee->first_name} {$employee->middle_initial} {$employee->last_name}"),
                'total_absent_ins' => $employee->total_absent_ins,
                'absent_status' => $getStatus($employee->total_absent_ins),
                'total_late_ins' => $employee->total_late_ins,
                'late_status' => $getStatus($employee->total_late_ins),
            ];
        });

        $this->employeesData = $result->values();

        // dd($this->employeesData);

    }
    public function closeModal()
    {
        $this->showModal = false;
    }
    public function render()
    {
        $this->totalPi = 0;
        $this->totalMp2 = 0;
        $this->totalMpl = 0;
        $this->totalCl = 0;
        $this->totalDareco = 0;
        $this->totalSssEcWisp = 0;

        $contributions = Contribution::all();
        foreach ($contributions as $contribution) {
            if ($contribution->hdmf_pi) {
                $jsonString = stripslashes($contribution->hdmf_pi);
                $data = json_decode($jsonString, true);

                if (is_array($data) && isset($data['ee_share'])) {
                    $eeShare = floatval($data['ee_share']);
                    $this->totalPi += $eeShare;
                }
            }
            if ($contribution->hdmf_mp2) {
                $data = json_decode(stripslashes($contribution->hdmf_mp2), true);
                if (is_array($data)) {
                    foreach ($data as $item) {
                        if (isset($item['ee_share'])) {
                            $this->totalMp2 += floatval($item['ee_share']);
                        }
                    }
                }
            }
            if ($contribution->hdmf_mpl) {
                $data = json_decode(stripslashes($contribution->hdmf_mpl), true);
                if (is_array($data) && isset($data['amount'])) {
                    $this->totalMpl += floatval($data['amount']);
                }
            }
            if ($contribution->hdmf_cl) {
                $data = json_decode(stripslashes($contribution->hdmf_cl), true);
                if (is_array($data) && isset($data['cl_amount'])) {
                    $this->totalCl += floatval($data['cl_amount']);
                }
            }
            if ($contribution->dareco) {
                $data = json_decode(stripslashes($contribution->dareco), true);
                if (is_array($data) && isset($data['amount'])) {
                    $this->totalDareco += floatval($data['amount']);
                }
            }
            // SSS, EC, WISP
            if ($contribution->sss) {
                $sssData = json_decode(stripslashes($contribution->sss), true);
                if (is_array($sssData) && isset($sssData['amount'])) {
                    $this->totalSssEcWisp += floatval($sssData['amount']);
                }
            }
            if ($contribution->ec) {
                $ecData = json_decode(stripslashes($contribution->ec), true);
                if (is_array($ecData) && isset($ecData['amount'])) {
                    $this->totalSssEcWisp += floatval($ecData['amount']);
                }
            }
            if ($contribution->wisp) {
                $wispData = json_decode(stripslashes($contribution->wisp), true);
                if (is_array($wispData) && isset($wispData['amount'])) {
                    $this->totalSssEcWisp += floatval($wispData['amount']);
                }
            }
        }

        $officeCounts = Employee::selectRaw("
        COALESCE(NULLIF(office_name, ''), designation) as office,
        COUNT(*) as count
    ")
            ->when($this->office, function ($query) {
                $query->whereRaw("COALESCE(NULLIF(office_name, ''), designation) = ?", [$this->office]);
            })
            ->when($this->search, function ($query) {
                $query->whereRaw("COALESCE(NULLIF(office_name, ''), designation) LIKE ?", ["%{$this->search}%"]);
            })
            ->groupBy('office')
            ->orderBy('office')
            ->paginate(5);

        $employees = Employee::get();

        $this->offices = $employees->map(function ($employee) {
            return $employee->office_name ?: $employee->designation;
        })->unique()->sort()->values()->all();

        return view('livewire.dashboard-data', [
            'officeCounts' => $officeCounts,
        ]);
    }

}
