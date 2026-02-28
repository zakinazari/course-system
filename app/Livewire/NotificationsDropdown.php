<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class NotificationsDropdown extends Component
{
    public $notifications;

    protected $listeners = [
        'notificationReceived' => 'refreshNotifications'
    ];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $this->notifications = auth()->user()->unreadNotifications;
    }

    public function readNotification($id)
    {
        $notification = auth()->user()
            ->unreadNotifications()
            ->find($id);

        if ($notification) {
            $notification->markAsRead();
            return redirect($notification->data['url']);
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}
