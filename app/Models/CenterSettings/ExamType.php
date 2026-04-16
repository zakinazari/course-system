<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_exam_types')
            ->withPivot('percentage')
            ->withTimestamps();
    }
}
