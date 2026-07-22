<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\Student;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\PhysicalBook;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class StudentBookFee extends Model
{
    protected $fillable = [
        'student_id',
        'physical_book_id',
        'type',
        'status',
        'branch_id',
        'price',
        'payment_date',
        'notes',
        'reason',
        'user_id',
        'processed_by'
    ];

     protected $casts = [
        'price' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function book()
    {
        return $this->belongsTo(PhysicalBook::class,'physical_book_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
