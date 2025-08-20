<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Designation;


class ShowEmployee extends Component
{
    use WithPagination;
    public $search = '';
    public $designation = '';
    public $sortOrder = '';
    public $deletingId = null;

    public $designations = [];

    public function mount()
    {
        $this->designations = Designation::pluck('designation')->unique()->sort()->values()->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDesignation()
    {
        $this->resetPage();
    }


    public function confirmDelete($id)
    {
        $this->deletingId = $id;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
    }

    public function deleteEmployeeConfirmed()
    {
        Employee::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->dispatch('success', message: 'Employee deleted.');
    }


    public function render()
    {
        $employees = Employee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->designation, function ($query) {
                $query->where('designation', $this->designation);
            })
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            })
            ->latest()
            ->paginate(10);



        return view('livewire.show-employee', [
            'employees' => $employees
        ]);
    }
}
