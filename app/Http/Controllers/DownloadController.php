<?php


namespace App\Http\Controllers;

use App\Models\Archived;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download($id)
    {
        $fileRecord = Archived::findOrFail($id);
        $filePath = 'archives/' . $fileRecord->filename;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, "File not found.");
        }

        return Storage::disk('public')->download($filePath, $fileRecord->filename);
    }
}
