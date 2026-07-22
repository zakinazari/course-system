<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Warehouse\BookInventory;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Settings\Menu;
class BookInventoryStockNotification extends Notification
{
   use Queueable;

    /**
     * Create a new notification instance.
     */
    
    public function __construct(public BookInventory $inventory)
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
            'broadcast'
        ];
    }

    public function toDatabase($notifiable)
    {

        $is_out_of_stock = $this->inventory->quantity == 0;

        $menu_id = Menu::where('slug', 'book_inventory')
            ->value('id');



        if (! $menu_id) {

            return;
        }


        $location = $this->inventory->warehouse->branch?->name ?? 'Central Warehouse';

        return [

            'type' => 'book_inventory_stock',


            'reference_id' => $this->inventory->id,


            'warehouse_id' => $this->inventory->warehouse_id,

            'book_id' => $this->inventory->book_id,

            'branch_id' => $this->inventory->warehouse->branch_id,


            'title' => $is_out_of_stock
                ? 'Book Out Of Stock'
                : 'Low Stock Alert',


            'message' => $is_out_of_stock
            ? "{$this->inventory->book->name} is out of stock in {$location}."
            : "{$this->inventory->book->name} stock reached {$this->inventory->quantity} in {$location}.",

            // مقدار همان لحظه
            'quantity' => $this->inventory->quantity,


            'minimum_stock' => $this->inventory?->book?->minimum_stock,


            'url' => route('book-inventory',$menu_id)//60 = active_menu_id
            .'?inventory='.$this->inventory->id
            .'&book_id='.$this->inventory->book_id
            .'&warehouse_id='.$this->inventory->warehouse_id,

        ];

    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(
            $this->toDatabase($notifiable)
        );
    }
}
