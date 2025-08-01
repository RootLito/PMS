<?php

namespace App\Livewire;


use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class Voucher extends Component
{
    use WithPagination;

    protected $employees;
    public function render()
    {
         $employees = Employee::with('rawCalculation')
            ->whereHas('rawCalculation', fn($q) => $q->where('is_completed', true))
            ->paginate(10);

        // Pass the paginated employees to the view
        return view('livewire.voucher', [
            'employees' => $employees,
        ]);
    }
}
