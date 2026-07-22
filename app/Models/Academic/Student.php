<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Gender;
use App\Models\CenterSettings\Occupation;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;
use App\Models\Assessment\StudentAttendance;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = [
        'student_number',
        'student_code',
        'name',
        'name_fa',
        'name_pa',
        'last_name',
        'last_name_fa',
        'last_name_pa',
        'father_name',
        'father_name_fa',
        'father_name_pa',
        'date_of_birth',
        'phone_no',
        'whats_app',
        'father_whats_app',
        'tazkira_no',
        'address',
        'status',
        'registration_date',
        'user_id',
        'branch_id',
        'gender_id',
        'occupation_id',  
    ];
    protected $casts = [
        'date_of_birth' => 'date',

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

    public function courseResults()
    {
        return $this->hasMany(StudentCourseResult::class, 'student_id');
    }

    public function examScores()
    {
        return $this->hasMany(StudentExamScore::class, 'student_id');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id');
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
        // static::creating(function ($student) {

        //     DB::transaction(function () use ($student) {

        //         // شماره دانشجو همیشه سیستم حساب می‌کند
        //         $lastNumber = self::withoutGlobalScope('branch')
        //             ->where('branch_id', $student->branch_id)
        //             ->max('student_number');

        //         $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        //         $student->student_number = $nextNumber;

        //         $branchCode = $student->branch->code ?? '';

        //         // اگر student_code دستی وارد نشده باشد، بساز
        //         if (empty($student->student_code)) {
        //             $student->student_code = $branchCode . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        //         }
        //     });
        // });

        // static::updating(function ($student) {
        //     // فقط اگر branch_id تغییر کرده یا student_code خالی باشد
        //     if ($student->isDirty('branch_id') || empty($student->student_code)) {
        //         DB::transaction(function () use ($student) {

        //             // شماره دانشجو همیشه سیستم حساب می‌کند
        //             $lastNumber = self::withoutGlobalScope('branch')
        //                 ->where('branch_id', $student->branch_id)
        //                 ->max('student_number');

        //             $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        //             $student->student_number = $nextNumber;

        //             $branchCode = $student->branch->code ?? '';

        //             if (empty($student->student_code)) {
        //                 $student->student_code = $branchCode . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        //             }
        //         });
        //     }
        // });

        static::creating(function ($student) {

            DB::transaction(function () use ($student) {

                $branchCode = $student->branch->code ?? 'BR';

                do {
                    $random = strtoupper(Str::random(5));
                    $code = $branchCode . '-' . $random;

                    $exists = self::withoutGlobalScope('branch')
                        ->where('student_code', $code)
                        ->exists();

                } while ($exists);
                if (empty($student->student_code)) {
                    $student->student_code = $code;
                }
            });
        });

        // static::updating(function ($student) {

        //     if ($student->isDirty('branch_id') || empty($student->student_code)) {

        //         DB::transaction(function () use ($student) {

        //             $branchCode = $student->branch->code ?? 'BR';

        //             do {
        //                 $random = strtoupper(Str::random(5));
        //                 $code = $branchCode . '-' . $random;

        //                 $exists = self::withoutGlobalScope('branch')
        //                     ->where('student_code', $code)
        //                     ->exists();

        //             } while ($exists);
        //             if (empty($student->student_code)) {
        //                 $student->student_code = $code;
        //             }
        //         });
        //     }
        // });
    }
}
