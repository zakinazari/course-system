<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'employee_number',
        'name',
        'last_name',
        'father_name',
        'phone_no',
        'email',
        'tazkira_no',
        'address',
        'status',
        'hire_date',
        'user_id',
        'branch_id',
    ];
    protected $casts = [
        'hire_date' => 'date',
    ];

    public function files()
    {
        return $this->hasMany(EmployeeFile::class,'employee_id');
    }

    public function photo()
    {
        return $this->hasOne(EmployeeFile::class,'employee_id')
            ->where('file_type',EmployeeFile::TYPE_PHOTO);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    public function employeeRoles()
    {
         return $this->belongsToMany(
            EmployeeRole::class,    // مدل مرتبط
            'employee_role_employee',   // جدول pivot
            'employee_id',            // کلید خارجی مدل جاری
            'employee_role_id'             // کلید خارجی مدل مرتبط
        )->withTimestamps();      // اگر جدول pivot timestamps دارد
    }

    public function isTeacher(): bool
    {
        return $this->employeeRoles->contains('name', 'Teacher');
    }

    public function isStaff(): bool
    {
        return $this->employeeRoles->contains('name', 'Staff');
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

        //  ساخت خودکار employee_code
        static::creating(function ($employee) {

            DB::transaction(function () use ($employee) {

                $lastNumber = self::withoutGlobalScope('branch')
                    ->where('branch_id', $employee->branch_id)
                    ->max('employee_number');

                $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

                $employee->employee_number = $nextNumber;

                $branchCode = $employee->branch->code;

                $employee->employee_code =
                    $branchCode . '-EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            });
        });

        static::updating(function ($employee) {
            
            if ($employee->isDirty('branch_id')) {
                DB::transaction(function () use ($employee) {
                    $lastNumber = self::withoutGlobalScope('branch')
                        ->where('branch_id', $employee->branch_id)
                        ->max('employee_number');

                    $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

                    $employee->employee_number = $nextNumber;

                    $branchCode = $employee->branch->code;

                    $employee->employee_code =
                        $branchCode . '-EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                });
            }
        });
    }
}
