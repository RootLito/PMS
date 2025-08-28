<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Archived;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;  
use Symfony\Component\HttpFoundation\StreamedResponse;

class Archive extends Component
{
    use WithPagination;
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
    public function render()
    {
        $files = Archived::orderBy('date_saved', 'desc')->paginate(7);
        return view('livewire.archive', [
            'files' => $files
        ]);
    }
}
