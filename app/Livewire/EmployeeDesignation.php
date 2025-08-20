<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Designation;
use Livewire\WithPagination;
class EmployeeDesignation extends Component
{
    use WithPagination;

    public $pap, $designation, $office;
    public $editId = null;
    public $editingId = null;
    public $editPap = '';
    public $editDesignation = '';
    public $editOffice = '';
    public $deletingId = null;
    public $search = '';

    protected $rules = [
        'pap' => 'nullable|string',
        'designation' => 'required|string',
        'office' => 'nullable|string',
    ];



    public function save()
    {
        $this->pap = trim($this->pap);
        $this->designation = trim($this->designation);
        $this->office = trim($this->office);

        $this->validate();

        if ($this->editId) {
            Designation::findOrFail($this->editId)->update([
                'pap' => $this->pap,
                'designation' => $this->designation,
                'office' => $this->office,
            ]);

            $this->dispatch('success', message: 'Designation updated!');
        } else {
            Designation::create([
                'pap' => $this->pap,
                'designation' => $this->designation,
                'office' => $this->office,
            ]);

            $this->dispatch('success', message: 'Designation added!');
        }

        $this->resetForm();
    }



    public function edit($id)
    {
        $designation = Designation::findOrFail($id);
        $this->editingId = $id;
        $this->editPap = $designation->pap;
        $this->editDesignation = $designation->designation;
        $this->editOffice = $designation->office;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'editPap', 'editDesignation', 'editOffice']);
    }

    public function updateDesignation()
    {
        $this->validate([
            'editPap' => 'nullable|string|max:255',
            'editDesignation' => 'required|string|max:255',
            'editOffice' => 'nullable|string|max:255',
        ]);

        Designation::where('id', $this->editingId)->update([
            'pap' => $this->editPap,
            'designation' => $this->editDesignation,
            'office' => $this->editOffice,
        ]);

        $this->dispatch('success', message: 'Designation updated!');
        $this->cancelEdit();
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
    }

    public function deleteDesignationConfirmed()
    {
        Designation::where('id', $this->deletingId)->delete();
        $this->dispatch('success', message: 'Designation deleted!');
        $this->deletingId = null;
    }

    public function resetForm()
    {
        $this->reset(['pap', 'designation', 'office', 'editId']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $designations = Designation::query()
            ->where('pap', 'like', '%' . $this->search . '%')
            ->orWhere('designation', 'like', '%' . $this->search . '%')
            ->orWhere('office', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(7);

        return view('livewire.employee-designation', [
            'designations' => $designations
        ]);
    }
}
