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
    public $previewData = null;
    public $previewFilename = null;

    public function redirectToEdit()
    {
        if (count($this->selectedEmployees) !== 1) {
            $this->dispatch('error', message: 'Please select only one employee to update.');
            return;
        }
        $employeeId = $this->selectedEmployees[0];
        return redirect()->to('/computation?employee_id=' . $employeeId);
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
    public function preview($fileId)
    {
        $file = Archived::findOrFail($fileId);

        // Use default disk (same as download method)
        $disk = config('filesystems.default');

        if (!Storage::disk($disk)->exists($file->filename)) {
            $this->dispatch('error', message: 'File not found for preview.');
            return;
        }

        try {
            $path = Storage::disk($disk)->path($file->filename);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();

            $data = [];
            foreach ($sheet->getRowIterator(1, 10) as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            $this->previewData = $data;
            $this->previewFilename = $file->filename;

            $this->dispatch('success', message: 'Preview loaded!');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error reading Excel file: ' . $e->getMessage());
        }
    }



    public function deleteFile($fileId)
    {
        $file = Archived::findOrFail($fileId);

        if (Storage::exists($file->filename)) {
            Storage::delete($file->filename);
        }

        $file->delete();

        $this->dispatch('success', message: 'File deleted successfully.');
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
