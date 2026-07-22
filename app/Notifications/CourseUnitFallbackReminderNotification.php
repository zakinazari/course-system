<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class CourseUnitFallbackReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public $courses,
        public $menu_id
    ) {}

    public function via(object $notifiable): array
    {
         return [
            'database',
            'broadcast'
        ];
    }
    
   public function toDatabase($notifiable)
    {
        return [

            'type' => 'course_unit_fallback',

            'title' => 'Course Unit Fallback',

            'message' => $this->courses->count()
                . ' courses dates were extended because units were continued.',

            'courses' => $this->courses->map(function ($course) {

                return [
                    'id' => $course->id,
                    'name' => $course->name,
                ];

            }),

            'url' => route('courses', [
                'menu_id' => $this->menu_id,
            ])
            . '?action=course_unit_fallback'
            . '&courses='
            . $this->courses->pluck('id')->implode(','),

        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(
            $this->toDatabase($notifiable)
        );
    }
}
