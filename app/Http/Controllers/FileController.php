<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Auth;
use App\Models\Website\WebPageFile;
class FileController extends Controller
{
    public function showWebPageFile($id)
    {
        $file = WebPageFile::findOrFail($id);
         abort_unless(Storage::exists($file->file_path), 404);

        return response()->file(
            Storage::path($file->file_path),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]
        );
    }
}
