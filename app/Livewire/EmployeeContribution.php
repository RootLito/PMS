<?php

namespace App\Livewire;


use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Contribution;
use Livewire\Component;
use Carbon\Carbon;

use App\Exports\HmdfMpl;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeContribution extends Component
{
    use WithPagination;
    public $search = '';
    public $designation = '';
    public $selectedContribution = '';
    public $percov = '';
    public $selectedEmployee = null;

    public $pag_ibig_id_rtn;

    // MPL ------------------------------------------
    public $application_number;
    public $loan_type;
    public $mpl_amount;
    public $status = '';
    public $start_te;
    public $end_te;
    public $mpl_remarks;
    public $notes;



    // MP2 -------------------------------------------
    public $mp2_account_number;
    public $mp2_mem_program;
    public $mp2_percov;
    public $ee_share;
    public $er_share;
    public $mp2_remarks;




    // PI/MC -------------------------------------------
    public $account_number;
    public $mem_program;
    public $pi_mc_percov;
    public $pi_mc_ee_share;
    public $pi_mc_er_share;
    public $pi_mc_remarks;
    public $pi_mc_amount;


    // CL -------------------------------------------
    public $cl_app_no;
    public $cl_loan_type;
    public $cl_amount;
    public $cl_remarks;
    public $cl_start_term;
    public $cl_end_term;



    // DARECO -------------------------------------------
    public $dareco_amount;
    public $dareco_remarks;


    // SSS | EC | WESP -------------------------------------------
    public $difference;
    public $remarks;

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
    // SELECT EMPLOYEE - FETCH DATA  ----------------------------------------------------------------
    public function employeeSelected($employeeId)
    {
        $this->selectedEmployee = $employeeId;
        $contribution = Contribution::where('employee_id', $employeeId)->first();
        if ($contribution) {
            $mpl = json_decode($contribution->hdmf_mpl, true) ?? [];
            $mp2 = json_decode($contribution->hdmf_mp2, true) ?? [];
            $pi_mc = json_decode($contribution->hdmf_pi, true) ?? [];
            $cl = json_decode($contribution->hdmf_cl, true) ?? [];
            $dareco = json_decode($contribution->dareco, true) ?? [];
            $sss = json_decode($contribution->sss, true) ?? [];
            $ec = json_decode($contribution->ec, true) ?? [];
            $wisp = json_decode($contribution->wisp, true) ?? [];

            $this->pag_ibig_id_rtn = $mpl['pag_ibig_id_rtn'] ?? null;

            // MPL
            $this->application_number = $mpl['app_no'] ?? null;
            $this->status = $mpl['status'] ?? '';
            $this->loan_type = $mpl['loan_type'] ?? null;
            $this->mpl_amount = $mpl['amount'] ?? null;
            $this->start_te = $mpl['start_te'] ?? null;
            $this->end_te = $mpl['end_te'] ?? null;
            $this->mpl_remarks = $mpl['remarks'] ?? null;
            $this->notes = $mpl['notes'] ?? null;

            // MP2
            $this->mp2_account_number = $mp2['account_number'] ?? null;
            $this->mp2_mem_program = $mp2['mem_program'] ?? null;
            $this->mp2_percov = $mp2['percov'] ?? $this->percov;
            $this->ee_share = $mp2['ee_share'] ?? null;
            $this->er_share = $mp2['er_share'] ?? null;
            $this->mp2_remarks = $mp2['remarks'] ?? null;

            // PI/MC
            $this->account_number = $pi_mc['app_no'] ?? null;
            $this->mem_program = $pi_mc['mem_program'] ?? null;
            $this->pi_mc_amount = $pi_mc['amount'] ?? null;
            $this->pi_mc_percov = $pi_mc['percov'] ?? $this->percov;
            $this->pi_mc_ee_share = $pi_mc['ee_share'] ?? null;
            $this->pi_mc_er_share = $pi_mc['er_share'] ?? null;
            $this->pi_mc_remarks = $pi_mc['remarks'] ?? null;


            //CL
            $this->cl_app_no = $cl['cl_app_no'] ?? null;
            $this->cl_loan_type = $cl['cl_loan_type'] ?? null;
            $this->cl_amount = $cl['cl_amount'] ?? null;
            $this->cl_remarks = $cl['cl_remarks'] ?? null;
            $this->cl_start_term = $cl['cl_start_term'] ?? null;
            $this->cl_end_term = $cl['cl_end_term'] ?? null;

            // DARECO
            $this->dareco_amount = $dareco['amount'] ?? null;
            $this->dareco_remarks = $dareco['remarks'] ?? null;

            // SSS, EC, WISP
            $this->contributions['sss']['amount'] = $sss['amount'] ?? null;
            $this->contributions['ec']['amount'] = $ec['amount'] ?? null;
            $this->contributions['wisp']['amount'] = $wisp['amount'] ?? null;

            // Remarks
            $this->remarks = $sss['remarks'] ?? $ec['remarks'] ?? $wisp['remarks'] ?? null;
            $this->difference = $sss['difference'] ?? $ec['difference'] ?? $wisp['difference'] ?? null;

        } else {
            $this->reset([
                'pag_ibig_id_rtn',
                'application_number',
                'loan_type',
                'mpl_amount',
                'start_te',
                'end_te',
                'mpl_remarks',
                'notes',
                'mp2_account_number',
                'mp2_mem_program',
                'mp2_percov',
                'ee_share',
                'er_share',
                'mp2_remarks',
                'account_number',
                'mem_program',
                'pi_mc_amount',
                'pi_mc_percov',
                'pi_mc_ee_share',
                'pi_mc_er_share',
                'pi_mc_remarks',
                'dareco_amount',
                'dareco_remarks',
                'contributions',
                'remarks',
                'difference',
                'cl_app_no',
                'cl_loan_type',
                'cl_amount',
                'cl_remarks',
                'cl_start_term',
                'cl_end_term',
            ]);
        }
    }
    // SAVE CONTRIBUTION ----------------------------------------------------------------
    public function saveContributions()
    {
        Contribution::updateOrCreate(
            ['employee_id' => $this->selectedEmployee],
            [
                'hdmf_pi' => json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'app_no' => $this->account_number,
                    'mem_program' => $this->mem_program,
                    'percov' => $this->pi_mc_percov,
                    'ee_share' => $this->pi_mc_ee_share,
                    'er_share' => $this->pi_mc_er_share,
                    'remarks' => $this->pi_mc_remarks,
                ]),

                'hdmf_mp2' => json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'account_number' => $this->mp2_account_number,
                    'mem_program' => $this->mp2_mem_program,
                    'percov' => $this->mp2_percov,
                    'ee_share' => $this->ee_share,
                    'er_share' => $this->er_share,
                    'remarks' => $this->mp2_remarks,
                ]),

                'hdmf_mpl' => json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'status' => $this->status,
                    'app_no' => $this->application_number,
                    'loan_type' => $this->loan_type,
                    'amount' => $this->mpl_amount,
                    'remarks' => $this->mpl_remarks,
                    'notes' => $this->notes,
                    'start_te' => $this->start_te,
                    'end_te' => $this->end_te,
                ]),

                'hdmf_cl' => json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'cl_app_no' => $this->cl_app_no,
                    'cl_loan_type' => $this->cl_loan_type,
                    'cl_amount' => $this->cl_amount,
                    'cl_remarks' => $this->cl_remarks,
                    'cl_start_term' => $this->cl_start_term,
                    'cl_end_term' => $this->cl_end_term,
                ]),

                'dareco' => json_encode([
                    'amount' => $this->dareco_amount,
                    'remarks' => $this->dareco_remarks,
                ]),

                'sss' => json_encode([
                    'amount' => $this->contributions['sss']['amount'],
                    'remarks' => $this->remarks,
                    'difference' => $this->difference,
                ]),

                'ec' => json_encode([
                    'amount' => $this->contributions['ec']['amount'],
                    'remarks' => $this->remarks,
                    'difference' => $this->difference,
                ]),

                'wisp' => json_encode([
                    'amount' => $this->contributions['wisp']['amount'],
                    'remarks' => $this->remarks,
                    'difference' => $this->difference,
                ]),
            ]
        );
        $this->selectedEmployee = null;
    }
    //EXPORT CONTRIBUTION ----------------------------------------------------------------
    public function exportContribution()
    {

        switch ($this->selectedContribution) {
            case 'hdmf_pi':
                // Handle HDMF - PI contribution logic
                // ...
                break;

            case 'hdmf_mp2':
                // Handle HDMF - MP2 contribution logic
                // ...
                break;

            case 'hdmf_mpl':
                $year = (String) Carbon::now()->year;
                return Excel::download(new HmdfMpl, "COS-MPL {$year}.xlsx");
            case 'hdmf_cl':
                // Handle HDMF - CL contribution logic
                // ...
                break;

            case 'dareco':
                // Handle DARECO contribution logic
                // ...
                break;

            case 'sss_ec_wisp':
                // Handle SSS, EC, WISP contribution logic
                // ...
                break;

            default:
                // Handle default case (e.g., no contribution selected)
                // ...
                break;
        }

    }


    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDesignation()
    {
        $this->resetPage();
    }
    public function mount()
    {
        $this->percov = Carbon::now()->format('Y-m');
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
        return view('livewire.employee-contribution', [
            'employees' => $employees
        ]);
    }
}
