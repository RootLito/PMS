<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Archived;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Archive extends Component
{
    use WithPagination;

    public $search = '';
    public $cutoff = '';
    public $dateSaved = '';
    public $month = '';
    public $year = '';
    public $months = [];
    public $years = [];
    public function download($fileId)
    {
        $file = Archived::findOrFail($fileId);
        $filePath = $file->filename;
        if (!Storage::exists($filePath)) {
            session()->flash('error', 'File not found.');
            return;
        }
        return Storage::download($filePath);
    }
    public function mount()
    {
        $this->initializeDateOptions();
    }
    public function initializeDateOptions()
    {
        $this->months = collect(range(1, 12))->mapWithKeys(function ($monthNumber) {
            return [$monthNumber => Carbon::create()->month($monthNumber)->format('F')];
        })->toArray();

        $currentYear = Carbon::now()->year;

        $this->years = range($currentYear, $currentYear - 10);

        // $this->month = Carbon::now()->month;
        // $this->year = $currentYear;
    }
    public function render()
    {
        $files = Archived::query()
            ->when(
                $this->search,
                fn($query, $search) =>
                $query->where('filename', 'like', '%' . $search . '%')
            )
            ->when($this->cutoff, fn($query, $cutoff) => $query->where('cutoff', $cutoff))
            ->when(in_array($this->dateSaved, ['asc', 'desc']), fn($query) => $query->orderBy('date_saved', $this->dateSaved))
            ->when(!in_array($this->dateSaved, ['asc', 'desc']), fn($query) => $query->orderBy('date_saved', 'desc'))
            ->when($this->month, fn($query, $month) => $query->where('month', $month))
            ->when($this->year, fn($query, $year) => $query->where('year', $year))
            ->paginate(10);
        return view('livewire.archive', [
            'files' => $files
        ]);
    }
}
