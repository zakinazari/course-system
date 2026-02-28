<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Student extends Model
{
    protected $fillable = [
        'student_number',
        'student_code',
        'name',
        'last_name',
        'father_name',
        'phone_no',
        'tazkira_no',
        'address',
        'status',
        'registration_date',
        'user_id',
        'branch_id',
    ];
    protected $casts = [
        'registration_date' => 'datetime',
    ];

    public function files()
    {
        return $this->hasMany(StudentFile::class,'st_id');
    }

    public function photo()
    {
        return $this->hasOne(StudentFile::class,'st_id')
            ->where('file_type', StudentFile::TYPE_PHOTO);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)
                    ->withPivot(['id','status', 'enrolled_at'])
                    ->withTimestamps();
    }
    
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    

     // شرط شعبه، سکوپ
    protected static function booted()
    {
        //  Global Scope شعبه
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

        //  ساخت خودکار student_code
        static::creating(function ($student) {

            DB::transaction(function () use ($student) {

                $lastNumber = self::withoutGlobalScope('branch')
                    ->where('branch_id', $student->branch_id)
                    ->max('student_number');

                $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

                $student->student_number = $nextNumber;

                $branchCode = $student->branch->code;

                $student->student_code =
                    $branchCode . '-ST-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            });
        });

        static::updating(function ($student) {
            
            if ($student->isDirty('branch_id')) {
                DB::transaction(function () use ($student) {
                    $lastNumber = self::withoutGlobalScope('branch')
                        ->where('branch_id', $student->branch_id)
                        ->max('student_number');

                    $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

                    $student->student_number = $nextNumber;

                    $branchCode = $student->branch->code;

                    $student->student_code =
                        $branchCode . '-ST-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                });
            }
        });
    }
}
