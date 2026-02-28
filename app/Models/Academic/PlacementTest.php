<?php

namespace App\Models\Academic;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Shift;
use App\Models\Academic\Student;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class PlacementTest extends Model
{
    protected $fillable = [
        'student_id',
        'program_id',
        'book_id',
        'shift_id',
        'branch_id',
        'user_id',
        'test_date',
        'expire_date',
        'fee_amount',
        'is_paid',
        'notes',
        'status',
    ];

    protected $casts = [
        'test_date' => 'date',
        'expire_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
