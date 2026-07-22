<?php

namespace App\Models\Documents;

use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\Student;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment\StudentCourseResult;
class Diploma extends Model
{
    protected $fillable = [
        'branch_id',
        'student_id',
        'serial_number',
        'verification_code',
        'graduated_at',
        'average',
        'is_revoked',
        'printed_at',
    ];

       protected $casts = [
        'graduated_at' => 'date',
        'is_revoked' => 'boolean',
        'printed_at' => 'datetime',
    ];

    // -------------------------------- relationships ----------------------------

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

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
    }

    public function getGradeAndAverage()
    {
        if ($this->average > 0) {
            $average = $this->average;
        } else {

            $result = StudentCourseResult::where('student_id', $this->student_id)
                ->where('status', 'passed')
                ->get();

            $total_course = $result->count();
            $total_score = $result->sum('total');

            $average = $total_course > 0
                ? round($total_score / $total_course, 2)
                : 0;
        }

        $grade = match (true) {
            $average >= 85 => 'A+',
            $average >= 80 => 'A',
            $average >= 75 => 'B+',
            $average >= 70 => 'B',
            $average >= 65 => 'C+',
            $average >= 60 => 'C',
            $average >= 55 => 'D+',
            $average >= 50 => 'D',
            $average >= 1  => 'F',
            default => '',
        };

        $this->average = $average;
        $this->grade = $grade;

        return $this;
    }
}
