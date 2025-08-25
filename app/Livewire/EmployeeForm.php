<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Designation;
use Livewire\Component;

class EmployeeForm extends Component
{
    public $last_name;
    public $first_name;
    public $middle_initial;
    public $suffix;
    public $employment_status = '';
    public $monthly_rate = '';
    public $gross;
    public $designation = '';
    public $office_code = '';
    public $office_name = '';
    public $designations = [];
    public $officeOptions = [];

    public function mount()
    {
        $designationsData = Designation::all();

        $this->designations = $designationsData
            ->pluck('designation')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        foreach ($designationsData as $item) {
            $this->officeOptions[$item->designation][$item->office] = $item->pap;
        }
    }
    public function updatedDesignation($value)
    {
        $this->office_name = '';
        $this->office_code = '';
    }
    public function updatedOfficeName()
    {
        if (isset($this->officeOptions[$this->designation][$this->office_name])) {
            $this->office_code = $this->officeOptions[$this->designation][$this->office_name];
        } else {
            $this->office_code = '';
        }
    }
    public function updatedMonthlyRate()
    {
        $this->gross = $this->monthly_rate ? number_format($this->monthly_rate / 2, 2, '.', '') : null;
    }
    public function save()
    {
        $validatedData = $this->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_initial' => 'nullable|string|max:100',
            'suffix' => 'nullable|string|max:5',
            'designation' => 'required|string',
            'office_name' => 'nullable|string',
            'office_code' => 'nullable|string',
            'employment_status' => 'required|string',
            'monthly_rate' => 'required|numeric',
            'gross' => 'required|numeric',
        ]);
        Employee::create($validatedData);
        $this->dispatch('success', message: 'Employee added.');
        $this->reset();
    }
    public function render()
    {
        $salaries = Salary::latest()->get();
        return view('livewire.employee-form', [
            'salaries' => $salaries
        ]);
    }
}
