<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    protected $fillable = [
        'submission_id',
        'file_type',
        'original_name',
        'file_path',
        'uploaded_by',
        'size',
        'mime',
        'round',
        'uploaded_at',
    ];
}
