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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getEmployeesWithExcess()
    {
        $allEmployees = Employee::query()
            ->with('contribution')
            ->orderBy('last_name', 'asc') 
            ->when($this->search, function ($query) {
                $query->where('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%');
            })
            ->get();

        $excessList = [];

        foreach ($allEmployees as $employee) {
            $contribution = $employee->contribution;

            if ($contribution) {
                $sss = is_array($contribution->sss) ? $contribution->sss : json_decode($contribution->sss, true);
                $ec = is_array($contribution->ec) ? $contribution->ec : json_decode($contribution->ec, true);
                $hdmf_pi = is_array($contribution->hdmf_pi) ? $contribution->hdmf_pi : json_decode($contribution->hdmf_pi, true);

                $sssAmount = $sss['amount'] ?? 0;
                $ecAmount = $ec['amount'] ?? 0;
                $sssSum = $sssAmount + $ecAmount;
                $displaySSS = ($sssSum > 760) ? ($sssSum - 760) : 0;

                $hdmfEeShare = $hdmf_pi['ee_share'] ?? 0;
                $displayHDMF = ($hdmfEeShare > 400) ? ($hdmfEeShare - 400) : 0;

                if ($displaySSS > 0 || $displayHDMF > 0) {
                    $employee->display_sss = $displaySSS;
                    $employee->display_hdmf = $displayHDMF;
                    $excessList[] = $employee;
                }
            }
        }

        $currentPage = $this->getPage();
        $perPage = 10;

        $currentItems = array_slice($excessList, ($currentPage - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $currentItems,
            count($excessList),
            $perPage,
            $currentPage,
            ['path' => url()->current()]
        );
    }

    public function render()
    {
        return view('livewire.contribution-excess', [
            'employees' => $this->getEmployeesWithExcess(),
        ]);
    }
}