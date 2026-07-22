<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class CourseStartReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct( public $courses,public $menu_id)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
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

            'type' => 'course_start_reminder',

            'title' => 'Upcoming Courses',

            'message' => count($this->courses) . ' courses will start tomorrow.',

            'courses' => $this->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'start_date' => $course->start_date,
                ];
            }),

            'url' => route('courses', [
                'menu_id' => $this->menu_id,
            ]) . '?action=upcoming&courses='
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
