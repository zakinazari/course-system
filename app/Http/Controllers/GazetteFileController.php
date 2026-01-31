<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Website\GazetteFile;
use App\Models\Website\RulingFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
class GazetteFileController extends Controller
{
   public function view($id)
    {
        $file = GazetteFile::findOrFail($id);

        $path = $file->file_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Disposition' => 'inline; filename="'.$file->file_name.'"'
        ]);
    }

   public function viewRulingFile($id)
    {
        $file = RulingFile::where('ruling_id',$id)->first();

        $path = $file->file_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Disposition' => 'inline; filename="'.$file->file_name.'"'
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'gazette_id' => 'required|exists:gazettes,id',
            'file'       => 'required|file|max:614400', // حجم 600MB
        ]);
        
        $file = $request->file('file');

        // کوتاه کردن نام فایل برای ذخیره
        $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'website/gazette-files/' . $request->gazette_id,
            $storedName,
            'local'
        );

        // ذخیره در دیتابیس
        $fileRecord = GazetteFile::create([
            'gazette_id' => $request->gazette_id,
            'file_path'  => $path,
            'file_name'  => $file->getClientOriginalName(),
            'file_type'  => $file->getClientOriginalExtension(),
            'file_size'  => Storage::disk('local')->size($path),
        ]);

        return response()->json([
            'success' => true,
            'file_id' => $fileRecord->id,
            'file_name' => $fileRecord->file_name,
        ]);
    }
}
