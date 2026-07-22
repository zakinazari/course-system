<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class CourseWaitingListComment extends Model
{
    protected $fillable = [
        'course_waiting_list_id',
        'comment',
        'user_id',
    ];


    public function waitingList()
    {
        return $this->belongsTo(CourseWaitingList::class, 'course_waiting_list_id');
    }

}
