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
        'course_type_id',
        'status',
        'user_id',
    ];

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'waiting'     => 'bg-warning',
            'enrolled' => 'bg-success',
            'cancelled'   => 'bg-primary',
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

    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class, 'course_type_id');
    }
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
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
