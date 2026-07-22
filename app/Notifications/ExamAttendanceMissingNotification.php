<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class ExamAttendanceMissingNotification extends Notification
{
   use Queueable;


    public function __construct(
        public $courses,
        public $menu_id
    ) {}


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

            $title = 'Missing Mid-Term Attendance';

            $message = $midtermCount . ' mid-term exam attendances have not been recorded.';

        } elseif ($finalCount && ! $midtermCount) {

            $title = 'Missing Final Attendance';

            $message = $finalCount . ' final exam attendances have not been recorded.';

        } else {

            $title = 'Missing Exam Attendance';

            $message = $this->courses->count() . ' exam attendances have not been recorded.';

        }


        return [

            'type' => 'exam_attendance_missing',

            'title' => $title,

            'message' => $message,

            'courses' => $this->courses,

            'url' => route('courses', [
                'menu_id' => $this->menu_id,
            ]) 
            . '?action=missing_exam_attendance'
            . '&exam_type=' . $this->getExamType()
            . '&courses=' . collect($this->courses)
                ->pluck('id')
                ->implode(','),

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
}
