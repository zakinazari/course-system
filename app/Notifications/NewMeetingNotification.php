<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class NewMeetingNotification extends Notification
{
     use Queueable;

    public $meeting;
    public $menu_id;

    public function __construct($meeting, $menu_id)
    {
        $this->meeting = $meeting;
        $this->menu_id = $menu_id;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'meeting_id' => $this->meeting->id,
            'title'      => 'New Meeting Request',
            'message'    => 'A new meeting has been registered for you.',
            'url'        => route('meetings', $this->menu_id)
                        . '?meeting=' . $this->meeting->id,
        ];
    }

}
