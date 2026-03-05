<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Contribution;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class ContributionExcess extends Component
{
    use WithPagination;

    public string $search = '';
    public $showPagIbigModal = false;
    public $showSSSModal = false;
    public $pagIbigAmount;
    public $sssAmount;



    // public function applyPagIbig()
    // {
    //     $this->validate([
    //         'pagIbigAmount' => 'required|numeric',
    //     ]);
    //     $contributions = Contribution::all();
    //     foreach ($contributions as $contribution) {
    //         $data = $contribution->hdmf_pi;
    //         $currentEeShare = (float) ($data['ee_share'] ?? 0);
    //         $newEeShare = $currentEeShare - (float) $this->pagIbigAmount;
    //         $data['ee_share'] = number_format(max(0, $newEeShare), 2, '.', '');
    //         $contribution->hdmf_pi = $data;
    //         $contribution->save();
    //     }

    //     $this->showPagIbigModal = false;
    //     $this->reset('pagIbigAmount');
    //     $this->dispatch('success', message: 'Pag-ibig deduction applied successfully.');
    // }

    // public function applySSS()
    // {
    //     $this->validate([
    //         'sssAmount' => 'required|numeric',
    //     ]);

    //     $contributions = Contribution::all();

    //     foreach ($contributions as $contribution) {
    //         $sssData = $contribution->sss;
    //         $ecData = $contribution->ec;
    //         $currentSss = (float) ($sssData['amount'] ?? 0);
    //         $currentEc = (float) ($ecData['amount'] ?? 0);
    //         $combinedTotal = $currentSss + $currentEc;
    //         $newTotal = max(0, $combinedTotal - (float) $this->sssAmount);
    //         $sssData['amount'] = number_format($newTotal, 2, '.', '');
    //         $ecData['amount'] = "0.00";
    //         $contribution->sss = $sssData;
    //         $contribution->ec = $ecData;
    //         $contribution->save();
    //     }

    //     $this->showSSSModal = false;
    //     $this->reset('sssAmount');
    //     $this->dispatch('success', message: 'SSS deduction applied successfully.');
    // }


    public function applyPagIbig()
    {
        $this->validate(['pagIbigAmount' => 'required|numeric']);
        $contributions = Contribution::all();
        foreach ($contributions as $contribution) {
            $data = $contribution->hdmf_pi;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            $currentEeShare = (float) ($data['ee_share'] ?? 0);
            $newEeShare = $currentEeShare - (float) $this->pagIbigAmount;
            $data['ee_share'] = number_format(max(0, $newEeShare), 2, '.', '');
            $contribution->hdmf_pi = $data;
            $contribution->save();
        }
        $this->showPagIbigModal = false;
        $this->reset('pagIbigAmount');
        $this->dispatch('success', message: 'Pag-ibig deduction applied successfully.');
    }

    public function applySSS()
    {
        $this->validate(['sssAmount' => 'required|numeric']);
        $contributions = Contribution::all();
        foreach ($contributions as $contribution) {
            $sssData = $contribution->sss;
            $ecData = $contribution->ec;
            if (is_string($sssData))
                $sssData = json_decode($sssData, true);
            if (is_string($ecData))
                $ecData = json_decode($ecData, true);
            $currentSss = (float) ($sssData['amount'] ?? 0);
            $currentEc = (float) ($ecData['amount'] ?? 0);
            $combinedTotal = $currentSss + $currentEc;
            $newTotal = max(0, $combinedTotal - (float) $this->sssAmount);
            $formattedNew = number_format($newTotal, 2, '.', '');
            $sssData['amount'] = $formattedNew;
            $ecData['amount'] = "0.00";
            $contribution->sss = $sssData;
            $contribution->ec = $ecData;
            $contribution->save();
        }
        $this->showSSSModal = false;
        $this->reset('sssAmount');
        $this->dispatch('success', message: 'SSS deduction applied successfully.');
    }



    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getEmployeesWithExcess()
    {
        return Employee::query()
            ->with('contribution')
            ->orderBy('last_name', 'asc')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->whereHas('contribution')
            ->paginate(10)
            ->through(function ($employee) {
                $contribution = $employee->contribution;

                $sss = is_string($contribution->sss) ? json_decode($contribution->sss, true) : $contribution->sss;
                $ec = is_string($contribution->ec) ? json_decode($contribution->ec, true) : $contribution->ec;
                $hdmf = is_string($contribution->hdmf_pi) ? json_decode($contribution->hdmf_pi, true) : $contribution->hdmf_pi;

                $sssVal = (float) ($sss['amount'] ?? 0);
                $ecVal = (float) ($ec['amount'] ?? 0);
                $hdmfVal = (float) ($hdmf['ee_share'] ?? 0);

                $employee->display_sss = $sssVal + $ecVal;
                $employee->display_hdmf = $hdmfVal;

                return $employee;
            });
    }

    public function render()
    {
        return view('livewire.contribution-excess', [
            'employees' => $this->getEmployeesWithExcess(),
        ]);
    }
}