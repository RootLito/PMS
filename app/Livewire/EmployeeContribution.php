<?php

namespace App\Livewire;


use App\Exports\HdmfCl;
use App\Exports\HdmfMp2;
use App\Exports\HdmfPi;
use App\Exports\SssEcWisp;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Contribution;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Designation;
use App\Exports\HmdfMpl;
use App\Exports\HmdfPi;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeContribution extends Component
{
    use WithPagination;
    public $search = '';
    public $nameMpl = '';
    public $designation = '';
    public $sortOrder = '';
    public $percov = '';
    public $selectedEmployee = null;
    public $employeeName = '';
    public array $selectedContributions = [];
    public string $selectedContribution = '';
    public bool $showContributions = false;

    public $showModal = false;

    public $month;
    public $year;
    public $months = [];
    public $years = [];
    public $newPercovValue;


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
    public $mp2Entries = [];
    public function addMp2Entry()
    {
        $this->mp2Entries[] = [
            'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
            'account_number' => '',
            'mem_program' => '',
            'percov' => $this->percov,
            'ee_share' => '',
            'er_share' => '',
            'remarks' => '',
        ];
    }

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
    public $sss_number;
    public $ec_number;
    public $wisp_number;
    public $difference;
    public $remarks;

    public $designations = [];
    public array $contributionLabels = [
        'hdmf_pi' => 'HDMF - PI',
        'hdmf_mp2' => 'HDMF - MP2',
        'hdmf_mpl' => 'HDMF - MPL',
        'hdmf_cl' => 'HDMF - CL',
        'dareco' => 'DARECO',
        'sss_ec_wisp' => 'SSS, EC, WISP',
    ];
    public $contributions = [
        'sss' => ['amount' => null],
        'ec' => ['amount' => null],
        'wisp' => ['amount' => null],
    ];

    //NOTIFICATION MODAL
    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }
    public function mount()
    {
        $this->percov = Carbon::now()->format('Y-m');
        $this->designations = Designation::pluck('designation')->unique()->sort()->values()->toArray();


        $this->months = collect(range(1, 12))->mapWithKeys(function ($monthNumber) {
            return [$monthNumber => Carbon::create()->month($monthNumber)->format('F')];
        })->toArray();

        $currentYear = Carbon::now()->year;

        $this->years = range($currentYear, $currentYear - 10);

        $this->month = Carbon::now()->month;
        $this->year = $currentYear;
    }
    public function resetContributionData()
    {
        $this->selectedContributions = [];
        $this->reset([
            'pag_ibig_id_rtn',
            'remarks',
            'difference',
            'application_number',
            'loan_type',
            'mpl_amount',
            'status',
            'start_te',
            'end_te',
            'mpl_remarks',
            'notes',
            'mp2Entries',
            'account_number',
            'mem_program',
            'pi_mc_percov',
            'pi_mc_ee_share',
            'pi_mc_er_share',
            'pi_mc_remarks',
            'pi_mc_amount',
            'cl_app_no',
            'cl_loan_type',
            'cl_amount',
            'cl_remarks',
            'cl_start_term',
            'cl_end_term',
            'dareco_amount',
            'dareco_remarks',
            'wisp_number',
            'ec_number',
            'sss_number',
        ]);
    }
    // SELECT EMPLOYEE X FETCH DATA  ---------------------------------------------------
    public function employeeSelected($employeeId)
    {
        $this->resetContributionData();
        $this->selectedEmployee = $employeeId;
        $contribution = Contribution::where('employee_id', $employeeId)->first();
        $employee = Employee::find($employeeId);
        $this->employeeName = $employee->last_name . ', ' . $employee->first_name;
        if (!empty($employee->suffix)) {
            $this->employeeName .= ' ' . $employee->suffix;
        }
        if (!empty($employee->middle_initial)) {
            $this->employeeName .= ' ' . strtoupper(substr($employee->middle_initial, 0, 1)) . '.';
        }
        if (!$contribution) {
            $this->selectedContributions = [];
            return;
        }
        $fields = [
            'hdmf_pi',
            'hdmf_mpl',
            'hdmf_mp2',
            'hdmf_cl',
            'dareco',
            'sss',
            'ec',
            'wisp',
        ];
        $selectedContributions = [];
        $sssEcWispGroup = [
            'sss' => false,
            'ec' => false,
            'wisp' => false,
        ];



        if ($contribution && $contribution->hdmf_mpl) {
            $jsonString = stripslashes($contribution->hdmf_mpl);
            $data = json_decode($jsonString, true);

            if (isset($data['end_te'])) {
                $endTermDate = Carbon::parse($data['end_te']);
                $today = Carbon::today();

                if ($endTermDate->isSameDay($today->copy()->addDay())) {
                    $employee = Employee::find($contribution->employee_id);
                    if ($employee) {
                        $firstName = $employee->first_name;
                        $middleInitial = $employee->middle_initial;
                        $lastName = $employee->last_name;
                        $suffix = $employee->suffix;
                        $middle = $middleInitial ? strtoupper(substr($middleInitial, 0, 1)) . '.' : '';
                        $suffixFormatted = $suffix ? $suffix . '.' : '';
                    }
                    $this->nameMpl = trim("{$firstName} {$middle} {$lastName} {$suffixFormatted}");
                    $this->showModal = true;
                }
            }
        }



        foreach ($fields as $field) {
            $jsonString = $contribution->$field;
            $decoded = json_decode($jsonString, true);

            if (is_array($decoded)) {
                $filtered = array_filter($decoded, function ($value) {
                    return !is_null($value) && $value !== '';
                });

                if (in_array($field, ['sss', 'ec', 'wisp'])) {
                    if (!empty($decoded['amount']) && floatval($decoded['amount']) > 0) {
                        $sssEcWispGroup[$field] = true;
                    }
                    continue;
                }

                if (!empty($filtered)) {
                    if (in_array($field, ['hdmf_pi', 'hdmf_mpl', 'hdmf_mp2', 'hdmf_cl'])) {
                        if (count($filtered) === 1 && array_key_exists('pag_ibig_id_rtn', $filtered)) {
                            continue;
                        }
                    }
                    $selectedContributions[] = $field;
                }
            }
        }

        if (in_array(true, $sssEcWispGroup, true)) {
            $selectedContributions[] = 'sss_ec_wisp';
        }

        $this->selectedContributions = $selectedContributions;



        if ($contribution) {
            $mpl = json_decode($contribution->hdmf_mpl, true) ?? [];
            $mp2 = json_decode($contribution->hdmf_mp2, true) ?? [];
            $pi_mc = json_decode($contribution->hdmf_pi, true) ?? [];
            $cl = json_decode($contribution->hdmf_cl, true) ?? [];
            $dareco = json_decode($contribution->dareco, true) ?? [];
            $sss = json_decode($contribution->sss, true) ?? [];
            $ec = json_decode($contribution->ec, true) ?? [];
            $wisp = json_decode($contribution->wisp, true) ?? [];

            $this->pag_ibig_id_rtn = $mpl['pag_ibig_id_rtn']
                ?? $mp2[0]['pag_ibig_id_rtn']
                ?? $pi_mc['pag_ibig_id_rtn']
                ?? $cl['pag_ibig_id_rtn']
                ?? null;

            // MPL
            $this->application_number = $mpl['app_no'] ?? null;
            $this->status = $mpl['status'] ?? '';
            $this->loan_type = $mpl['loan_type'] ?? null;
            $this->mpl_amount = $mpl['amount'] ?? null;
            $this->start_te = $mpl['start_te'] ?? null;
            $this->end_te = $mpl['end_te'] ?? null;
            $this->mpl_remarks = $mpl['remarks'] ?? null;
            $this->notes = $mpl['notes'] ?? null;

            // MP2 (MULTIPLE)
            $this->mp2Entries = json_decode($contribution->hdmf_mp2, true) ?? [];
            foreach ($this->mp2Entries as &$entry) {
                if (!isset($entry['percov']) || $entry['percov'] === '') {
                    $entry['percov'] = $this->percov;
                }
            }

            // PI/MC
            $this->account_number = $pi_mc['app_no'] ?? null;
            $this->mem_program = $pi_mc['mem_program'] ?? null;
            $this->pi_mc_amount = $pi_mc['amount'] ?? null;
            $this->pi_mc_percov = !empty($pi_mc['percov']) ? $pi_mc['percov'] : $this->percov;
            $this->pi_mc_ee_share = $pi_mc['ee_share'] ?? null;
            $this->pi_mc_er_share = $pi_mc['er_share'] ?? null;
            $this->pi_mc_remarks = $pi_mc['remarks'] ?? null;

            // CL
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
            $this->sss_number = $sss['amount'] ?? null;
            $this->ec_number = $ec['amount'] ?? null;
            $this->wisp_number = $wisp['amount'] ?? null;

            // Remarks
            $this->remarks = $sss['remarks'] ?? $ec['remarks'] ?? $wisp['remarks'] ?? null;
            $this->difference = $sss['difference'] ?? $ec['difference'] ?? $wisp['difference'] ?? null;
        }
    }
    // SAVE CONTRIBUTION ---------------------------------------------------------------
    public function saveContributions()
    {
        Contribution::updateOrCreate(
            ['employee_id' => $this->selectedEmployee],
            [
                'hdmf_pi' => in_array('hdmf_pi', $this->selectedContributions) ? json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'app_no' => $this->account_number,
                    'mem_program' => $this->mem_program,
                    'percov' => $this->pi_mc_percov,
                    'ee_share' => $this->pi_mc_ee_share,
                    'er_share' => $this->pi_mc_er_share,
                    'remarks' => $this->pi_mc_remarks,
                ]) : null,

                'hdmf_mp2' => in_array('hdmf_mp2', $this->selectedContributions) ? json_encode($this->mp2Entries) : null,



                'hdmf_mpl' => in_array('hdmf_mpl', $this->selectedContributions) ? json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'status' => $this->status,
                    'app_no' => $this->application_number,
                    'loan_type' => $this->loan_type,
                    'amount' => $this->mpl_amount,
                    'remarks' => $this->mpl_remarks,
                    'notes' => $this->notes,
                    'start_te' => $this->start_te,
                    'end_te' => $this->end_te,
                ]) : null,

                'hdmf_cl' => in_array('hdmf_cl', $this->selectedContributions) ? json_encode([
                    'pag_ibig_id_rtn' => $this->pag_ibig_id_rtn,
                    'cl_app_no' => $this->cl_app_no,
                    'cl_loan_type' => $this->cl_loan_type,
                    'cl_amount' => $this->cl_amount,
                    'cl_remarks' => $this->cl_remarks,
                    'cl_start_term' => $this->cl_start_term,
                    'cl_end_term' => $this->cl_end_term,
                ]) : null,

                'dareco' => in_array('dareco', $this->selectedContributions) ? json_encode([
                    'amount' => $this->dareco_amount,
                    'remarks' => $this->dareco_remarks,
                ]) : null,

                'sss' => in_array('sss_ec_wisp', $this->selectedContributions) ? json_encode([
                    'amount' => $this->sss_number,
                    'remarks' => $this->remarks,
                    'difference' => $this->difference,
                ]) : null,

                'ec' => in_array('sss_ec_wisp', $this->selectedContributions) ? json_encode([
                    'amount' => $this->ec_number,
                    'remarks' => $this->remarks,
                    'difference' => $this->difference,
                ]) : null,

                'wisp' => in_array('sss_ec_wisp', $this->selectedContributions) ? json_encode([
                    'amount' => $this->wisp_number,
                    'remarks' => $this->remarks,
                    'difference' => $this->difference,
                ]) : null,

            ]
        );
        $this->dispatch('success', message: 'Contribution added.');
    }
    // DELETE---------------------------------------------------------------------------
    public function deleteContribution($contribution)
    {
        $groupMap = [
            'sss_ec_wisp' => ['sss', 'ec', 'wisp'],
        ];
        $fieldsToDelete = $groupMap[$contribution] ?? [$contribution];
        $this->selectedContributions = array_filter(
            $this->selectedContributions,
            fn($item) => !in_array($item, array_keys($groupMap)) && !in_array($item, $fieldsToDelete)
        );
        $updateData = [];
        foreach ($fieldsToDelete as $field) {
            $updateData[$field] = null;
        }
        Contribution::where('employee_id', $this->selectedEmployee)
            ->update($updateData);

        $this->dispatch('success', message: 'Contribution deleted.');
    }
    // DELETE ACCOUNT-------------------------------------------------------------------
    public function deleteAccount($employeeId, $accountNumber)
    {
        $employee = Contribution::where('employee_id', $employeeId)->firstOrFail();

        $hdmfMp2 = json_decode($employee->hdmf_mp2, true);

        if (!is_array($hdmfMp2)) {
            $this->mp2Entries = [];
            return;
        }

        $filtered = array_filter($hdmfMp2, function ($entry) use ($accountNumber) {
            return $entry['account_number'] !== $accountNumber;
        });

        $filtered = array_values($filtered);

        $employee->hdmf_mp2 = json_encode($filtered);
        $employee->save();

        $this->mp2Entries = $filtered;
        $this->dispatch('success', message: 'Account deleted.');
    }
    public function toggleContributions()
    {
        $this->showContributions = !$this->showContributions;
    }
    public function confirmContributions()
    {
        $this->showContributions = false;
    }
    //UPDATE PERCOV
    public function updatePercov()
    {
        $newPercov = $this->year . str_pad($this->month, 2, '0', STR_PAD_LEFT);
        $contributions = Contribution::whereNotNull('hdmf_pi')->get();
        foreach ($contributions as $contribution) {
            $hdmfPiRaw = $contribution->hdmf_pi;
            $decoded = json_decode($hdmfPiRaw, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            if (is_array($decoded)) {
                $decoded['percov'] = $newPercov;
                $contribution->hdmf_pi = json_encode($decoded);
                $contribution->save();

            }
        }
        $this->dispatch('success', message: 'PerCov Updated!');
    }
    //EXPORT CONTRIBUTION --------------------------------------------------------------
    public function exportContribution()
    {
        if (!$this->selectedContribution) {
            $this->dispatch('error', message: 'Please select a contribution type.');
            return;
        }

        switch ($this->selectedContribution) {
            case 'hdmf_pi':
                $year = (String) Carbon::now()->year;
                return Excel::download(new HdmfPi, "COS-MC {$year}.xlsx");
            case 'hdmf_mp2':
                $year = (String) Carbon::now()->year;
                return Excel::download(new HdmfMp2, "COS-MP2 {$year}.xlsx");
            case 'hdmf_mpl':
                $year = (String) Carbon::now()->year;
                return Excel::download(new HmdfMpl, "COS-MPL {$year}.xlsx");
            case 'hdmf_cl':
                $year = (String) Carbon::now()->year;
                return Excel::download(new HdmfCl, "COS-CAL {$year}.xlsx");
            case 'dareco':
                break;

            case 'sss_ec_wisp':
                $year = (String) Carbon::now()->year;
                return Excel::download(new SssEcWisp(), "COS-SSS {$year}.xlsx");
            default:
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
            ->paginate(10);
        return view('livewire.employee-contribution', [
            'employees' => $employees
        ]);
    }
}
