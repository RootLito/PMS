<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contribution;
use App\Models\Employee;
use Carbon\Carbon;

class DashboardData extends Component
{
    public $reminderData = [];
    public $joCount = 0;
    public $cosCount = 0;
    public $totalCount = 0;
    public $maleCount = 0;
    public $femaleCount = 0;
    public $showModal = false;

    public function mount()
    {
        $this->loadReminderData();
        $this->joCount = Employee::where('employment_status', 'JO')->count();
        $this->cosCount = Employee::where('employment_status', 'COS')->count();
        $this->totalCount = $this->joCount + $this->cosCount;
        $this->maleCount = Employee::where('gender', 'male')->count();
        $this->femaleCount = Employee::where('gender', 'female')->count();


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




    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.dashboard-data');
    }

}
