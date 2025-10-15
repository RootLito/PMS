<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Designation;
use Livewire\Component;
use App\Models\Position;
class UpdateEmployee extends Component
{
    public $employeeId;
    public $employee;
    public $last_name;
    public $first_name;
    public $middle_initial;
    public $suffix;
    public $employment_status = '';
    public $designation = '';
    public $designation_pap = '';
    public $office_code = '';
    public $office_name = '';
    public $designations = [];
    public $designationMap = [];
    public $officeOptions = [];
    public $monthly_rate;
    public $gross;
    public $position;
    public $gender;
    public $order_no;



    public function mount($id)
    {
        $this->employeeId = $id;

        $employee = Employee::findOrFail($id);

        $this->last_name = $employee->last_name;
        $this->first_name = $employee->first_name;
        $this->middle_initial = $employee->middle_initial;
        $this->suffix = $employee->suffix;
        $this->employment_status = $employee->employment_status;
        $this->designation = $employee->designation;
        $this->designation_pap = $employee->designation_pap;
        $this->office_code = $employee->office_code;
        $this->office_name = $employee->office_name;
        $this->monthly_rate = $employee->monthly_rate;
        $this->gross = $employee->gross;
        $this->position = $employee->position;
        $this->gender = $employee->gender;
        $this->order_no = $employee->order_no;

        $designationsData = Designation::all();

        $this->designations = $designationsData
            ->pluck('designation')
            ->unique()
            ->values()
            ->toArray();

        foreach ($designationsData as $item) {
            $this->officeOptions[$item->designation][$item->office] = $item->pap;
            $this->designationMap[$item->designation] = $item->pap;
        }
    }
    public function updatedDesignation($value)
    {
        $this->office_name = '';
        $this->officePap = '';
        $this->designation_pap = $this->designationMap[$value] ?? '';
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
            'order_no' => 'required|integer',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_initial' => 'nullable|string|max:100',
            'suffix' => 'nullable|string|max:5',
            'designation' => 'required|string',
            'designation_pap' => 'nullable|string',
            'office_name' => 'nullable|string',
            'office_code' => 'nullable|string',
            'employment_status' => 'required|string',
            'monthly_rate' => 'required|numeric',
            'gross' => 'required|numeric',
            'position' => 'required|String',
            'gender' => 'required|String',
        ]);

        $employee = Employee::findOrFail($this->employeeId);
        $employee->update($validatedData);

        $this->dispatch('success', message: 'Employee updated.');
    }


    public function render()
    {
        $salaries = Salary::latest()->get();
        $positions = Position::get();

        return view('livewire.update-employee', [
            'salaries' => $salaries,
            'positions' => $positions
        ]);
    }
}
