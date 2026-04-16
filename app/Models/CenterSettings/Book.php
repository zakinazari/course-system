<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'abbreviation',
        'status',
        'program_id',
        'fee',
        'pass_mark',
        'total_teaching_days',
        'min_capacity',
        'max_capacity',
        'exam_fine_amount',
        'level_number',
        'drop_days',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function examTypes()
    {
        return $this->belongsToMany(ExamType::class, 'book_exam_types')
            ->withPivot('percentage')
            ->withTimestamps();
    }
}
