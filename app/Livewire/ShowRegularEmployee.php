<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Designation;
use App\Exports\ExportPayslip;



class ShowRegularEmployee extends Component
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
    // public function downloadPayslip($employeeId)
    // {
    //     $employee = Employee::with(['contribution', 'rawCalculation'])->findOrFail($employeeId);

    //     $fullName = trim(
    //         $employee->first_name . ' ' .
    //         ($employee->middle_initial ? strtoupper(substr($employee->middle_initial, 0, 1)) . '. ' : '') .
    //         $employee->last_name .
    //         ($employee->suffix ? ' ' . $employee->suffix . '.' : '')
    //     );

    //     $mp2Data = optional($employee->contribution)->hdmf_mp2;
    //     $mp2Total = null;

    //     if ($mp2Data) {
    //         $mp2Array = json_decode($mp2Data, true);
    //         if (is_array($mp2Array)) {
    //             $mp2Total = array_sum(array_column($mp2Array, 'amount'));
    //         }
    //     }

    //     $data = [
    //         'full_name' => $fullName,
    //         'position' => $employee->position,
    //         'gross_monthly_income' => $employee->monthly_rate,
    //         'contributions' => [
    //             'hdmf_pi' => isset($employee->contribution->hdmf_pi)
    //                 ? json_decode($employee->contribution->hdmf_pi, true)['ee_share'] ?? null
    //                 : null,
    //             'hdmf_mpl' => isset($employee->contribution->hdmf_mpl)
    //                 ? json_decode($employee->contribution->hdmf_mpl, true)['amount'] ?? null
    //                 : null,
    //             'hdmf_mp2' => $mp2Total,
    //             'hdmf_cl' => isset($employee->contribution->hdmf_cl)
    //                 ? json_decode($employee->contribution->hdmf_cl, true)['cl_amount'] ?? null
    //                 : null,

    //             'dareco' => isset($employee->contribution->dareco)
    //                 ? json_decode($employee->contribution->dareco, true)['dareco_amount'] ?? null
    //                 : null,

    //             'sss' => isset($employee->contribution->sss)
    //                 ? json_decode($employee->contribution->sss, true)['amount'] ?? null
    //                 : null,

    //             'ec' => isset($employee->contribution->ec)
    //                 ? json_decode($employee->contribution->ec, true)['amount'] ?? null
    //                 : null,

    //             'wisp' => isset($employee->contribution->wisp)
    //                 ? json_decode($employee->contribution->wisp, true)['amount'] ?? null
    //                 : null,

    //         ],
    //         'total_absent_late' => optional($employee->rawCalculation)->total_absent_late,
    //         'tax' => optional($employee->rawCalculation)->tax,
    //     ];


    //     $data = [
    //         'full_name' => $fullName,
    //         'position' => $employee->position,
    //         'gross_monthly_income' => $employee->monthly_rate,
    //         'contributions' => [
    //             'hdmf_pi' => isset($employee->contribution->hdmf_pi)
    //                 ? json_decode($employee->contribution->hdmf_pi, true)['ee_share'] ?? null
    //                 : null,
    //             'hdmf_mpl' => isset($employee->contribution->hdmf_mpl)
    //                 ? json_decode($employee->contribution->hdmf_mpl, true)['amount'] ?? null
    //                 : null,

    //             'hdmf_mp2' => optional($employee->contribution)->hdmf_mp2,
    //             'hdmf_cl' => isset($employee->contribution->hdmf_cl)
    //                 ? json_decode($employee->contribution->hdmf_cl, true)['cl_amount'] ?? null
    //                 : null,

    //             'dareco' => isset($employee->contribution->dareco)
    //                 ? json_decode($employee->contribution->dareco, true)['dareco_amount'] ?? null
    //                 : null,

    //             'sss' => isset($employee->contribution->sss)
    //                 ? json_decode($employee->contribution->sss, true)['amount'] ?? null
    //                 : null,

    //             'ec' => isset($employee->contribution->ec)
    //                 ? json_decode($employee->contribution->ec, true)['amount'] ?? null
    //                 : null,

    //             'wisp' => isset($employee->contribution->wisp)
    //                 ? json_decode($employee->contribution->wisp, true)['amount'] ?? null
    //                 : null,

    //         ],
    //         // 'total_absent_late' => optional($employee->rawCalculation)->total_absent_late,
    //         'total_absent_late' => $employee->rawCalculations->sum(function ($calc) {
    //             return floatval($calc->total_absent_late ?? 0);
    //         }),
    //         'tax' => optional($employee->rawCalculation)->tax,
    //     ];

    //     // dd($data);

    //     $export = new ExportPayslip($data);
    //     $fileContents = $export->generate();

    //     return response()->streamDownload(
    //         fn() => print ($fileContents),
    //         "payslip_{$employeeId}.docx",
    //         [
    //             'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    //         ]
    //     );
    // }


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
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            })
            ->paginate(10);
        return view('livewire.show-regular-employee', [
            'employees' => $employees
        ]);
    }
}
