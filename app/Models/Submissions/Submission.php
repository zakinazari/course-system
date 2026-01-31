<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Issues\Issue;
class Submission extends Model
{


    protected $table = 'submissions';

    protected $fillable = [
        'submitter_id',
        'title_fa',
        'title_en',
        'abstract_fa',
        'abstract_en',
        'comments_to_editor_fa',
        'comments_to_editor_en',
        'round',
        'status',
        'issue_id',
        'submitted_at',
        'last_activity_at',
        'views',
        'accepted_abstract_id',
        'main_axis_id',
        'sub_axis_id',
        'upload_status',
        
    ];

    protected $dates = [
        'submitted_at',
        'last_activity_at',
        'deleted_at',
    ];

    public function authors()
    {
        return $this->hasMany(SubmissionAuthor::class, 'submission_id');
    }
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }
    public function files()
    {
        return $this->hasMany(SubmissionFile::class, 'submission_id');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'submission_keywords', 'submission_id', 'keyword_id')
                    ->withTimestamps();
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }
}
