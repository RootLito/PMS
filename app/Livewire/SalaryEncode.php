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

// public function updatedMonthlyRate($value)
// {
//     if ($value) {
//         $this->daily_rate = floor((float)$value / 22, 2);  
//         $this->halfday_rate = floor($this->daily_rate / 2, 2);  
//         $this->hourly_rate = floor($this->halfday_rate / 4, 2);  
//         $this->per_min_rate = floor($this->hourly_rate / 60, 2);  
//     }
// }


public function updatedMonthlyRate($value)
{
    if ($value) {
        $this->daily_rate = floor((float)$value / 22 * 100) / 100;  
        $this->halfday_rate = floor($this->daily_rate / 2 * 100) / 100;  
        $this->hourly_rate = floor($this->halfday_rate /4 * 100) / 100; 
        $this->per_min_rate = floor($this->hourly_rate /60 * 100) / 100; 
    }
}


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

    public function delete($id)
    {
        Salary::find($id)->delete();
        session()->flash('message', 'Salary record deleted successfully!');
    }

    public function resetFields()
    {
        $this->salaryId = null;
        $this->monthly_rate = null;
        $this->daily_rate = null;
        $this->halfday_rate = null;
        $this->hourly_rate = null;
        $this->per_min_rate = null;
        $this->isUpdating = false;
    }

    public function render()
    {
        $salaries = Salary::latest()->paginate(10);
        return view('livewire.salary-encode', compact('salaries'));
    }
}
