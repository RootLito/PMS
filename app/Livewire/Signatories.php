<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Signatory;
use App\Models\Assigned;
use Livewire\WithPagination;

class Signatories extends Component
{
    use WithPagination;

    public $name;
    public $designation;
    public $prapared_by = '';
    public $noted_by = '';
    public $funds_availability = '';
    public $approved_by = '';
    public $allSignatories;
    public $assigned;


    public $editingId = null;
    public $deletingId = null;
    public $editName = '';
    public $editDesignation = '';






    public function mount()
    {
        $this->allSignatories = Signatory::latest()->get();
        $this->assigned = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    }

    protected $rules = [
        'name' => 'required|string|min:5|max:255',
        'designation' => 'required|string|min:5|max:255',
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
        $this->allSignatories = Signatory::latest()->get();
    }

    public function startEdit($id)
    {
        $signatory = Signatory::findOrFail($id);
        $this->editingId = $id;
        $this->editName = $signatory->name;
        $this->editDesignation = $signatory->designation;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->reset(['editName', 'editDesignation']);
    }

    public function updateSignatory()
    {
        $this->validate([
            'editName' => 'required|string|min:2|max:255',
            'editDesignation' => 'required|string|min:2|max:255',
        ]);

        $signatory = Signatory::findOrFail($this->editingId);
        $signatory->update([
            'name' => $this->editName,
            'designation' => $this->editDesignation,
        ]);

        $this->editingId = null;
        $this->reset(['editName', 'editDesignation']);
        session()->flash('message', 'Signatory updated successfully.');

        $this->allSignatories = Signatory::latest()->get();
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

    public function deleteSignatoryConfirmed()
    {
        Signatory::findOrFail($this->deletingId)->delete();

        session()->flash('message', 'Signatory deleted successfully.');

        $this->deletingId = null;

        $this->allSignatories = Signatory::latest()->get();
        $this->resetPage();

        if ($this->editingId === $this->deletingId) {
            $this->cancelEdit();
        }
    }

    public function saveSignatory()
    {
        $this->validate([
            'prapared_by' => 'required|string',
            'noted_by' => 'required|string',
            'funds_availability' => 'required|string',
            'approved_by' => 'required|string',
        ]);

        Assigned::create([
            'prapared_by' => $this->prapared_by,
            'noted_by' => $this->noted_by,
            'funds_availability' => $this->funds_availability,
            'approved_by' => $this->approved_by,
        ]);

        session()->flash('message', 'Signatories assigned successfully.');

        $this->assigned = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();

        $this->reset([
            'prapared_by',
            'noted_by',
            'funds_availability',
            'approved_by',
        ]);
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
