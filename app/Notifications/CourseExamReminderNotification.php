<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class CourseExamReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $courses, public $menu_id)
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
        $midtermCount = collect($this->courses)
            ->where('exam_type', 'mid_term')
            ->count();

        $finalCount = collect($this->courses)
            ->where('exam_type', 'final')
            ->count();

        if ($midtermCount && ! $finalCount) {

            $title = __('label.midterm_exams_tomorrow');

            $message = __('label.courses_have_midterm_exams_tomorrow', [
                'count' => $midtermCount
            ]);

        } elseif ($finalCount && ! $midtermCount) {

            $title = __('label.final_exams_tomorrow');

            $message = __('label.courses_have_final_exams_tomorrow', [
                'count' => $finalCount
            ]);

        } else {

            $title = __('label.upcoming_exams');

            $message = __('label.courses_have_scheduled_exams_tomorrow', [
                'count' => $this->courses->count()
            ]);

        }

        return [

            'type' => 'course_exam_reminder',

            'title' => $title,

            'message' => $message,

            'courses' => $this->courses,

            'url' => route('courses', [
            'menu_id' => $this->menu_id,
            ]) . '?action=exam_tomorrow'
                . '&exam_type=' . $this->getExamType()
                . '&courses=' . collect($this->courses)->pluck('id')->implode(','),

        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(
            $this->toDatabase($notifiable)
        );
    }


    private function getExamType()
    {
        $types = collect($this->courses)
            ->pluck('exam_type')
            ->unique();

        if ($types->count() === 1) {

            return $types->first();

        }

        return 'all';
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
