<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use Livewire\Component;

class EmployeeForm extends Component
{
    public $last_name;
    public $first_name;
    public $middle_initial;
    public $suffix;
    public $employment_status = '';
    public $designation = '';
    public $office_code = '';
    public $office_name = '';
    public $monthly_rate = '';
    public $gross;






    public $designations = [
        "PFO DAVAO ORIENTAL",
        "Operation and Management of Production Facilities - TOS TAGABULI",
        "PFO DAVAO DEL SUR",
        "General Management and Supervision-PFO DAVAO DEL NORTE",
        "TOS NABUNTURAN",
        "PFO DAVAO DE ORO",
        "PFO DAVAO OCCIDENTAL",
        "MULTI-SPECIES HATCHERY- BATO",
        "CFO DAVAO CITY",
        "SAAD",
        "FPSSD (LGU Assisted)",
        "Monitoring, Control and Surveillance - FMRED",
        "FISHERIES MANAGEMENT, REGULATORY AND ENFORCEMENT DIVISION (FMRED)",
        "Research and Development - NSAP",
        "Extension, Support, Education and Training Services (ESETS)",
        "Fisheries Laboratory Section",
        "General Management and Supervision - ORD",
        "Development of Organizational Policies, Plans & Procedures",
        "Fisheries Inspection and Quarantine Unit",
        "Regional Adjudication and Committee Secretariat",
        "Regional Fisheries Information Management Unit - RFIMU",
        "FPSSD",
    ];
    public $officeOptions = [
        'PFO DAVAO ORIENTAL' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'Driver' => '100000100001000',
        ],
        'Operation and Management of Production Facilities - TOS TAGABULI' => [
            'Operation and Management of Production Facilities - TOS TAGABULI' => '310102100002000',
            'Operation and Management of Production Facilities - TOS TAGABULI ' => '310102100001000',
        ],
        'PFO DAVAO DEL SUR' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'Driver' => '',
        ],
        'General Management and Supervision-PFO DAVAO DEL NORTE' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'PFO / MP-PANABO_IGACOS' => '310102100001000',
            'Driver' => '100000100001000',
        ],
        'TOS NABUNTURAN' => [
            'TOS - Broodstock Production and Maintenance' => '310102100001000',
            'TOS - Freshwater Fingerlings / Seed Production' => '310102100001000',
            'General Management and Supervision' => '100000100001000',
            'BASIL' => '',
            'WATCHMAN' => '',
        ],
        'PFO DAVAO DE ORO' => [
            'General Management and Supervision PFO - Mariculture' => '310300100001000',
            'FLDT' => '310300100001000',
            'Driver' => '100000100001000',
        ],
        'PFO DAVAO OCCIDENTAL' => [
            'Extension, Support, Education and Training Services (ESETS) - FLDT' => '310300100001000',
            'Driver' => '100000100001000',
            'MP Tubalan' => '310102100001000',
        ],
        'MULTI-SPECIES HATCHERY- BATO' => [
            'Multi-Species Hatchery' => '310102100002000',
            'Bay Management' => '310200100004000',
        ],
        'CFO DAVAO CITY' => [
            'General Management and Supervision' => '100000100001000',
            'FLDT' => '310300100001000',
        ],
        'SAAD' => [
            'PFO DAVAO OCCIDENTAL' => '310105200001000',
            'PFO DAVAO DEL SUR' => '310105200001000',
            'PFO DAVAO DEL NORTE' => '310105200001000',
            'PFO DAVAO ORIENTAL' => '310105200001000',
            'REGIONAL OFFICE' => '310105200001000',
            'SAAD-MAED' => '310105200001000',
        ],
        'FPSSD (LGU Assisted)' => [
            'FPSSD Norwegian Cage/Fish Pen/Mariculture Park' => '',
            'FPSSD PFO Davao Oriental' => '',
        ],
        'Monitoring, Control and Surveillance - FMRED' => [
            'ERMCSOC' => '310200100001000',
            'Driver' => '310200100001000',
            'Region' => '310200100001000',
            'CFVGL' => '310200100003000',
        ],
        "FISHERIES MANAGEMENT, REGULATORY AND ENFORCEMENT DIVISION (FMRED)" => [
            "IMEMS" => '310200100001000'
        ],
        'Research and Development - NSAP' => [
            'Research and Development - NSAP' => '200000100002',
        ],
        'Extension, Support, Education and Training Services (ESETS)' => [
            'Fisherfolk Coordination Unit' => '310300100001000',
            'Training Unit' => '310300100001000',
            'Information Unit' => '310300100001000',
        ],
        'Fisheries Laboratory Section' => [
            'HAB Monitoring' => '310200100002000',
            'Chem Lab' => '310200100001000',
            'Microbiology Lab' => '310200100002000',
        ],
        'General Management and Supervision - ORD' => [
            'Budget' => '100000100001000',
            'Accounting' => '100000100001000',
            'GSU' => '100000100001000',
            'Driver' => '100000100001000',
            'Admin' => '100000100001000',
            'Cashier' => '100000100001000',
            'BAC' => '100000100001000',
            'COA' => '100000100001000',
            'ORD' => '100000100001000',
            'GAD' => '100000100001000',
            'FSP' => '100000100001000',
            'GSU - Handyman' => '100000100001000',
            'HRMU' => '100000100001000',
        ],
        'Development of Organizational Policies, Plans & Procedures' => [
            'PMEU' => '200000100001000',
        ],
        'Fisheries Inspection and Quarantine Unit' => [
            'Fisheries Inspection and Quarantine Unit' => '310200100004000',
        ],
        'Regional Adjudication and Committee Secretariat' => [
            'Regional Adjudication and Committee Secretariat' => '200000100003000',
        ],
        'Regional Fisheries Information Management Unit - RFIMU' => [
            'Regional Fisheries Information Management Unit - RFIMU' => '',
        ],
        'FPSSD' => [
            'FPSSD - EPSDP' => '',
            "PHMS - Philippine Salt Industry Dev’t Project" => '',
        ],
    ];


    public function updatedDesignation($value)
    {
        $this->office_name = '';
        $this->office_code = '';
    }
    public function updatedOfficeName($value)
    {
        $this->office_code = $this->officeOptions[$this->designation][$value] ?? '';
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

        session()->flash('message', 'Employee successfully added.');

        return redirect()->route('employee.new');
    }
    public function render()
    {

        $salaries = Salary::latest()->get();
        return view('livewire.employee-form', [
            'salaries' => $salaries
        ]);
    }
}
