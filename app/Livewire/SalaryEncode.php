<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Salary;
use Livewire\WithPagination;
class SalaryEncode extends Component
{
    use WithPagination;
    public $salaryId, $monthly_rate, $daily_rate, $halfday_rate, $hourly_rate, $per_min_rate;
    public $isUpdating = false;

    // Store the data (Create/Update)
    public function save()
    {
        $this->validate([
            'monthly_rate' => 'required|numeric',
            'daily_rate' => 'required|numeric',
            'halfday_rate' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'per_min_rate' => 'required|numeric',
        ]);

        if ($this->isUpdating) {
            $salary = Salary::find($this->salaryId);
            $salary->update([
                'monthly_rate' => $this->monthly_rate,
                'daily_rate' => $this->daily_rate,
                'halfday_rate' => $this->halfday_rate,
                'hourly_rate' => $this->hourly_rate,
                'per_min_rate' => $this->per_min_rate,
            ]);
            session()->flash('message', 'Salary record updated successfully!');
        } else {
            Salary::create([
                'monthly_rate' => $this->monthly_rate,
                'daily_rate' => $this->daily_rate,
                'halfday_rate' => $this->halfday_rate,
                'hourly_rate' => $this->hourly_rate,
                'per_min_rate' => $this->per_min_rate,
            ]);
            session()->flash('message', 'Salary record created successfully!');
        }

        $this->resetFields();
    }

    // Edit the salary record
    public function edit($id)
    {
        $salary = Salary::find($id);
        $this->salaryId = $salary->id;
        $this->monthly_rate = $salary->monthly_rate;
        $this->daily_rate = $salary->daily_rate;
        $this->halfday_rate = $salary->halfday_rate;
        $this->hourly_rate = $salary->hourly_rate;
        $this->per_min_rate = $salary->per_min_rate;

        $this->isUpdating = true;
    }

    // Delete the salary record
    public function delete($id)
    {
        Salary::find($id)->delete();
        session()->flash('message', 'Salary record deleted successfully!');
    }

    // Reset form fields
    public function resetFields()
    {
        $this->salaryId = null;
        $this->monthly_rate = '';
        $this->daily_rate = '';
        $this->halfday_rate = '';
        $this->hourly_rate = '';
        $this->per_min_rate = '';
        $this->isUpdating = false;
    }

    // Render the component
    public function render()
    {
        $salaries = Salary::paginate(10);
        return view('livewire.salary-encode', compact('salaries'));
    }
}