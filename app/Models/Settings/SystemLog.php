<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Hr\Employee;
class SystemLog extends Model
{
    protected $fillable = [
        'user_id',
        'section',
        'st_id',
        's_id',
        'type_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class,'st_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'s_id');
    }
}
