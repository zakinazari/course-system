<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Shift;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CourseWaitingList extends Model
{
    protected $fillable = [
        'student_id',
        'branch_id',
        'program_id',
        'book_id',
        'shift_id',
        'phone_no',
        'father_no',
        'comment',
        'status',
        'comment',
        'user_id',
    ];

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'placement'     => 'bg-info',
            'passed' => 'bg-success',
            'failed'   => 'bg-danger',
            'makeup'   => 'bg-warning',
            'dropped'   => 'bg-secondary',
        };
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    public function comments()
    {
        return $this->hasMany(CourseWaitingListComment::class, 'course_waiting_list_id','id');
    }
    // شرط شعبه، سکوپ
    protected static function booted()
    {
        static::addGlobalScope('branch', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            if ($user->isDeveloper() || $user->isAdmin()) {
                return;
            }
 
            $builder->where('branch_id', $user->branch_id);
        });
    }
}
