<?php

namespace App\Livewire;


use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Contribution;
use Livewire\Component;

class EmployeeContribution extends Component
{
    use WithPagination;
    public $search = '';
    public $designation = '';
    public $selectedEmployee = null;
    public $remarks;
    public $mp2_remarks;

    public $contributions = [
        'sss' => ['amount' => null],
        'ec' => ['amount' => null],
        'wisp' => ['amount' => null],
    ];
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



    public function employeeSelected($employeeId)
    {
        $this->selectedEmployee = $employeeId;
    }

    public function saveContributions()
    {
        $employee = Employee::find($this->selectedEmployee);

        if (!$employee) {
            return;
        }

        Contribution::create([
            'hdmf_pi' => json_encode([
                'employee_id' => $employee->id,
                'amount' => $this->contributions['hdmf_pi']['amount'] ?? null,
                'remarks' => $this->contributions['hdmf_pi']['remarks'] ?? null,
            ]),
            'hdmf_mp2' => json_encode([
                'employee_id' => $employee->id,
                'percov' => $this->contributions['hdmf_mp2']['percov'] ?? null,
                'ee_share' => $this->contributions['hdmf_mp2']['ee_share'] ?? null,
                'er_share' => $this->contributions['hdmf_mp2']['er_share'] ?? null,
                'remarks' => $this->mp2_remarks,
            ]),
            'hdmf_mpl' => json_encode([
                'employee_id' => $employee->id,
                'pag_ibig_id_rtn' => $this->contributions['hdmf_mpl']['pag_ibig_id_rtn'] ?? null,
                'app_no' => $this->contributions['hdmf_mpl']['app_no'] ?? null,
                'loan_type' => $this->contributions['hdmf_mpl']['loan_type'] ?? null,
                'amount' => $this->contributions['hdmf_mpl']['amount'] ?? null,
                'remarks' => $this->contributions['hdmf_mpl']['remarks'] ?? null,
                'note' => $this->contributions['hdmf_mpl']['note'] ?? null,
                'start_te' => $this->contributions['hdmf_mpl']['start_te'] ?? null,
                'end_te' => $this->contributions['hdmf_mpl']['end_te'] ?? null,
            ]),
            'hdmf_cl' => json_encode([
                'employee_id' => $employee->id,
                'pag_ibig_id_rtn' => $this->contributions['hdmf_cl']['pag_ibig_id_rtn'] ?? null,
                'app_no' => $this->contributions['hdmf_cl']['app_no'] ?? null,
                'mem_program' => $this->contributions['hdmf_cl']['mem_program'] ?? null,
                'amount' => $this->contributions['hdmf_cl']['amount'] ?? null,
                'percov' => $this->contributions['hdmf_cl']['percov'] ?? null,
                'ee_share' => $this->contributions['hdmf_cl']['ee_share'] ?? null,
                'er_share' => $this->contributions['hdmf_cl']['er_share'] ?? null,
                'remarks' => $this->contributions['hdmf_cl']['remarks'] ?? null,
            ]),
            'dareco' => json_encode([
                'employee_id' => $employee->id,
                'amount' => $this->contributions['dareco']['amount'] ?? null,
                'remarks' => $this->contributions['dareco']['remarks'] ?? null,
            ]),
            'sss' => json_encode([
                'employee_id' => $employee->id,
                'amount' => $this->contributions['sss']['amount'],
                'remarks' => $this->remarks,
            ]),
            'ec' => json_encode([
                'employee_id' => $employee->id,
                'amount' => $this->contributions['ec']['amount'],
                'remarks' => $this->remarks,
            ]),
            'wisp' => json_encode([
                'employee_id' => $employee->id,
                'amount' => $this->contributions['wisp']['amount'],
                'remarks' => $this->remarks,
            ]),
        ]);

        // dd([
        //     'hdmf_pi' => json_encode([
        //         'employee_id' => $employee->id,
        //         'amount' => $this->contributions['hdmf_pi']['amount'] ?? null,
        //         'remarks' => $this->contributions['hdmf_pi']['remarks'] ?? null,
        //     ]),
        //     'hdmf_mp2' => json_encode([
        //         'employee_id' => $employee->id,
        //         'percov' => $this->contributions['hdmf_mp2']['percov'] ?? null,
        //         'ee_share' => $this->contributions['hdmf_mp2']['ee_share'] ?? null,
        //         'er_share' => $this->contributions['hdmf_mp2']['er_share'] ?? null,
        //         'remarks' => $this->mp2_remarks,
        //     ]),
        //     'hdmf_mpl' => json_encode([
        //         'employee_id' => $employee->id,
        //         'pag_ibig_id_rtn' => $this->contributions['hdmf_mpl']['pag_ibig_id_rtn'] ?? null,
        //         'app_no' => $this->contributions['hdmf_mpl']['app_no'] ?? null,
        //         'loan_type' => $this->contributions['hdmf_mpl']['loan_type'] ?? null,
        //         'amount' => $this->contributions['hdmf_mpl']['amount'] ?? null,
        //         'remarks' => $this->contributions['hdmf_mpl']['remarks'] ?? null,
        //         'note' => $this->contributions['hdmf_mpl']['note'] ?? null,
        //         'start_te' => $this->contributions['hdmf_mpl']['start_te'] ?? null,
        //         'end_te' => $this->contributions['hdmf_mpl']['end_te'] ?? null,
        //     ]),
        //     'hdmf_cl' => json_encode([
        //         'employee_id' => $employee->id,
        //         'pag_ibig_id_rtn' => $this->contributions['hdmf_cl']['pag_ibig_id_rtn'] ?? null,
        //         'app_no' => $this->contributions['hdmf_cl']['app_no'] ?? null,
        //         'mem_program' => $this->contributions['hdmf_cl']['mem_program'] ?? null,
        //         'amount' => $this->contributions['hdmf_cl']['amount'] ?? null,
        //         'percov' => $this->contributions['hdmf_cl']['percov'] ?? null,
        //         'ee_share' => $this->contributions['hdmf_cl']['ee_share'] ?? null,
        //         'er_share' => $this->contributions['hdmf_cl']['er_share'] ?? null,
        //         'remarks' => $this->contributions['hdmf_cl']['remarks'] ?? null,
        //     ]),
        //     'dareco' => json_encode([
        //         'employee_id' => $employee->id,
        //         'amount' => $this->contributions['dareco']['amount'] ?? null,
        //         'remarks' => $this->contributions['dareco']['remarks'] ?? null,
        //     ]),
        //     'sss' => json_encode([
        //         'employee_id' => $employee->id,
        //         'amount' => $this->contributions['sss']['amount'],
        //         'remarks' => $this->remarks,
        //     ]),
        //     'ec' => json_encode([
        //         'employee_id' => $employee->id,
        //         'amount' => $this->contributions['ec']['amount'],
        //         'remarks' => $this->remarks,
        //     ]),
        //     'wisp' => json_encode([
        //         'employee_id' => $employee->id,
        //         'amount' => $this->contributions['wisp']['amount'],
        //         'remarks' => $this->remarks,
        //     ]),
        // ]);

    }






    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDesignation()
    {
        $this->resetPage();
    }



    public function render()
    {
        // dd(Contribution::all());
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



        return view('livewire.employee-contribution', [
            'employees' => $employees
        ]);
    }
}
