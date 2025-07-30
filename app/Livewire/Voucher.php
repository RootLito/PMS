<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class Voucher extends Component
{
    use WithPagination;
    public function render()
    {
        
        return view('livewire.voucher');
    }
}
