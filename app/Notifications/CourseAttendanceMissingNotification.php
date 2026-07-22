<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class CourseAttendanceMissingNotification extends Notification
{
     use Queueable;

    public function __construct(
        public $shift,
        public $courses,
        public $menu_id
    ) {

        
    }

    public function via(object $notifiable): array
    {
        return [
            'database',
            'broadcast',
        ];
    }


    public function toDatabase($notifiable)
    {
        return [

            'type' => 'course_attendance_missing',

            'title' => 'Missing Attendance',

            'message' => count($this->courses) 
                . ' courses have no attendance recorded for '
                . $this->shift->name
                . ' shift.',

            'shift' => [
                'id' => $this->shift->id,
                'name' => $this->shift->name,
            ],

            'shift_id' => $this->shift->id,
            'branch_id' => $this->courses->first()?->branch_id,
            'courses' => $this->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                ];
            }),

            'url' => route('courses', [
                'menu_id' => $this->menu_id,
            ]) . '?action=missing-attendance&courses=' . $this->courses->pluck('id')->implode(','),

        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(
            $this->toDatabase($notifiable)
        );
    }
}
