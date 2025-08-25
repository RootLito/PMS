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
    public $deletingId = null;
    public function updatedMonthlyRate($value)
    {
        if ($value) {
            $this->daily_rate = floor((float) $value / 22 * 100) / 100;
            $this->halfday_rate = floor($this->daily_rate / 2 * 100) / 100;
            $this->hourly_rate = floor($this->halfday_rate / 4 * 100) / 100;
            $this->per_min_rate = floor($this->hourly_rate / 60 * 100) / 100;
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
            $this->dispatch('success', message: 'Salary updated!');
        } else {
            Salary::create([
                'monthly_rate' => $this->monthly_rate,
                'daily_rate' => $this->daily_rate,
                'halfday_rate' => $this->halfday_rate,
                'hourly_rate' => $this->hourly_rate,
                'per_min_rate' => $this->per_min_rate,
            ]);
            $this->dispatch('success', message: 'New salary saved!');

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


    public function confirmDelete($id)
    {
        $this->deletingId = $id;
    }
    public function cancelDelete()
    {
        $this->deletingId = null;
    }
    public function deleteConfirmed()
    {
        $salary = Salary::find($this->deletingId);

        if ($salary) {
            $salary->delete();
            $this->dispatch('success', message: 'Salary deleted.');
        } else {
            $this->dispatch('error', message: 'Salary not found.');
        }

        $this->deletingId = null;
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
        $salaries = Salary::latest()->paginate(13);
        return view('livewire.salary-encode', compact('salaries'));
    }
}
