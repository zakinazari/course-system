<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class NotificationsDropdown extends Component
{
   public $notifications = [];


    protected $listeners = [
        'notificationReceived' => 'refreshNotifications'
    ];



    public function mount()
    {
        $this->refreshNotifications();
    }



    public function refreshNotifications()
    {
        $this->notifications = auth()->user()
            ->unreadNotifications()
            ->latest()
            ->get();
    }



    public function readNotification($id)
    {

        $notification = auth()->user()
            ->unreadNotifications()
            ->where('id',$id)
            ->first();



        if($notification){

            $notification->markAsRead();


            $this->refreshNotifications();


            return redirect($notification->data['url']);

        }

    }




    public function markAllAsRead()
    {

        auth()->user()
            ->unreadNotifications()
            ->update([
                'read_at'=>now()
            ]);



        $this->refreshNotifications();

    }





    public function render()
    {
        return view('livewire.notifications-dropdown');
    }

}
