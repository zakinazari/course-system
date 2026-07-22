<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documents\Diploma;
use App\Models\Assessment\StudentCourseResult;
use Auth;
use Illuminate\Support\Str;
use DB;
use App\Services\QrCodeService;
class DiplomaController extends Controller
{
    public function verify($verification_code)
    {

        $diploma = Diploma::with('student','student.photo')
            ->where('verification_code', $verification_code)
            ->first();

        if($diploma){

            $diploma->getGradeAndAverage();

            $qrService = app(QrCodeService::class);
            $url = route('diploma.verify', $diploma->verification_code);
            $diploma->qr_code = $qrService->diplomaQrGenerate($url);
        }
        return view('livewire.documents.diplomas.verify', compact('diploma'));
    }
}
