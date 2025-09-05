<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Contribution;
use Livewire\Component;
use Carbon\Carbon;

class Reminder extends Component
{
    public $reminder = false;
    public $showModal = false;
    public $reminderData = [];

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

    public function redirectToComputation($employeeId)
    {
        return redirect()->to('/contribution?employee_id=' . $employeeId);
    }

    public function mount()
    {
        $this->loadReminderData();
    }

    public function reminderToggle()
    {
        $this->reminder = !$this->reminder;
        $this->loadReminderData();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.reminder', [
            'reminderData' => $this->reminderData,
        ]);
    }
}
