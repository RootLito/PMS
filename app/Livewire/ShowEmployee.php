<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Employee;

class ShowEmployee extends Component
{
    use WithPagination;
    public $search = '';
    public $designation = '';
    public $designations = [
        "CFO DAVAO CITY",
        "Development of Organizational Policies, Plans & Procedures",
        "Extension, Support, Education and Training Services (ESETS)",
        "Fisheries Inspection and Quarantine Unit",
        "Fisheries Laboratory Section",
        "FPSSD",
        "FPSSD (LGU Assisted)",
        "General Management and Supervision - ORD",
        "General Management and Supervision-PFO DAVAO DEL NORTE",
        "Monitoring, Control and Surveillance - FMRED",
        "MULTI-SPECIES HATCHERY- BATO",
        "Operation and Management of Production Facilities - TOS TAGABULI",
        "PFO DAVAO DE ORO",
        "PFO DAVAO DEL SUR",
        "PFO DAVAO OCCIDENTAL",
        "PFO DAVAO ORIENTAL",
        "Regional Adjudication and Committee Secretariat",
        "Regional Fisheries Information Management Unit - RFIMU",
        "SAAD",
        "TOS NABUNTURAN"
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDesignation()
    {
        $this->resetPage();
    }

    
    public function deleteEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);

        if ($employee) {
            $employee->delete();
            session()->flash('message', 'Employee successfully deleted.');
        } else {
            session()->flash('error', 'Employee not found.');
        }
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
            ->paginate(10);



        return view('livewire.show-employee', [
            'employees' => $employees
        ]);
    }
}
