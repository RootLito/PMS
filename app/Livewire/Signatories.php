<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Signatory;
use Livewire\WithPagination;

class Signatories extends Component
{
    use WithPagination;
    public $name;
    public $designation;

    public $prepared, $noted_by, $funds_availability, $approved;

    public $allSignatories;

    public function mount()
    {
        $this->allSignatories = Signatory::latest()->get();
    }
    protected $rules = [
    'name' => 'required|string|min:5|max:255',
    'designation' => 'required|string|min:5|max:255',
];

protected $roleRules = [
    'prepared' => 'required|exists:signatories,id',
    'noted_by' => 'required|exists:signatories,id',
    'funds_availability' => 'required|exists:signatories,id',
    'approved' => 'required|exists:signatories,id',
];

public function save()
{
    $this->validate();

    Signatory::create([
        'name' => $this->name,
        'designation' => $this->designation,
    ]);

    session()->flash('message', 'Signatory added successfully.');

    $this->reset(['name', 'designation']);
    $this->resetPage();
    $this->allSignatories = Signatory::all();
}

public function updateRoles()
{
    $this->validate($this->roleRules); 

    $roles = [
        'prepared' => $this->prepared,
        'noted_by' => $this->noted_by,
        'funds_availability' => $this->funds_availability,
        'approved' => $this->approved,
    ];

    foreach ($roles as $role => $signatoryId) {
        $signatory = Signatory::find($signatoryId);
        if ($signatory) {
            $signatory->role = ucfirst(str_replace('_', ' ', $role)); 
            $signatory->save();
        }
    }

    session()->flash('message', 'Roles updated successfully.');

    $this->reset(['prepared', 'noted_by', 'funds_availability', 'approved']);
}

    public function render()
    {
        $signatories = Signatory::latest()->paginate(5);

       return view('livewire.signatories', [
        'signatories' => $signatories,
        'allSignatories' => $this->allSignatories,
    ]);
    }
}
