<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class BookExemptionRequestNotification extends Notification
{
    use Queueable;
    public $student;
    public $book;
    public $menu_id;

    public function __construct($student, $book, $menu_id)
    {
        $this->student = $student;
        $this->book = $book;
        $this->menu_id = $menu_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
         return [
            'type'         => 'book_exemption_request',
            'reference_id' => $this->student->id,

            'student_id'   => $this->student->id,
            'book_id'      => $this->book['id'],

            'title'        => 'Book Exemption Request',

            'message'      => sprintf(
                'Student %s (%s) has requested a book exemption for "%s".',
                $this->student->name,
                $this->student->student_code,
                $this->book['name'] ?? ''
            ),

            'url' => route('student-financial-profile', [
                'menu_id'    => $this->menu_id,
                'student_id' => encrypt($this->student->id),
            ]) . '?action=exemption',
        ];
    }


    public function toBroadcast($notifiable)
    {
       
        return new BroadcastMessage(
            $this->toDatabase($notifiable)
        );
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
