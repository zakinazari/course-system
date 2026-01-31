<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;
use Storage;
class UploadSubmissionFiles implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $submission_id,
        public int $user_id,
        public array $files 
    ) {}

    public function handle()
    {
        DB::beginTransaction();

        try {
            
            $submission = Submission::findOrFail($this->submission_id);

            $submission->update([
                'upload_status' => 'processing',
            ]);

            
            if ($submission->status === 'revision_required') {
                $submission->update([
                    'round'  => $submission->round + 1,
                    'status' => 'submitted',
                ]);
            }

            $current_round = $submission->round;

    
            foreach ($this->files as $file) {

                $ext = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                $storedName = Str::random(40) . '.' . $ext;
                $finalPath = 'submissions/' . $this->submission_id . '/' . $storedName;

                Storage::disk('local')->move($file['path'], $finalPath);

                SubmissionFile::create([
                    'submission_id' => $this->submission_id,
                    'file_type'     => 'manuscript',
                    'original_name' => $file['original_name'],
                    'file_path'     => $finalPath,
                    'uploaded_by'   => $this->user_id,
                    'size'          => Storage::disk('local')->size($finalPath),
                    'mime'          => Storage::disk('local')->mimeType($finalPath),
                    'round'         => $current_round,
                    'uploaded_at'   => now(),
                ]);
            }

            DB::commit();
            
            Submission::where('id', $this->submission_id)
            ->update(['upload_status' => 'done']);
        } catch (\Throwable $e) {

            DB::rollBack();

            Submission::where('id', $this->submission_id)
                ->update(['upload_status' => 'failed']);

            throw $e;
        }
    }

}
