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
    public $office_name = '';
    public $designationPap = '';
    public $officePap = '';
    public $designations = [];
    public $officeOptions = [];
    public $designationMap = [];

    public function mount()
    {
        $this->runDesignation();
    }


    public function runDesignation()
    {
        $designationsData = Designation::all();
        $this->designations = [];
        $this->designationMap = [];
        $this->officeOptions = [];

        foreach ($designationsData as $item) {
            $this->designations[$item->designation] = $item->designation;
            $this->designationMap[$item->designation] = $item->pap;

            if (!empty($item->office)) {
                $this->officeOptions[$item->designation][$item->office] = $item->office_pap;
            }
        }
    }




    public function updatedDesignation($value)
    {
        $this->office_name = '';
        $this->officePap = '';
        $this->designationPap = $this->designationMap[$value] ?? '';
    }
    public function updatedOfficeName($value)
    {
        $this->officePap = $this->officeOptions[$this->designation][$value] ?? '';
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
            'suffix' => 'nullable|string|max:20',
            'designation' => 'required|string',
            'designationPap' => 'nullable|string',
            'office_name' => 'nullable|string',
            'officePap' => 'nullable|string',
            'employment_status' => 'required|string',
            'monthly_rate' => 'required|numeric',
            'gross' => 'required|numeric',
        ]);

        $validatedData['designation_pap'] = $this->designationPap;
        $validatedData['office_code'] = $this->officePap;

        Employee::create($validatedData);

        $this->dispatch('success', message: 'Employee added.');
        $this->reset();
        $this->runDesignation();

    }



    public function render()
    {
        $salaries = Salary::latest()->get();
        return view('livewire.employee-form', [
            'salaries' => $salaries
        ]);
    }
}
