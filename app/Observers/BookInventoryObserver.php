<?php

namespace App\Observers;

use App\Models\Warehouse\BookInventory;
use App\Models\User;
use App\Notifications\BookInventoryStockNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Settings\NotificationCategory;
class BookInventoryObserver
{
    /**
     * Handle the BookInventory "created" event.
     */
    public function created(BookInventory $bookInventory): void
    {
        //
    }

    /**
     * Handle the BookInventory "updated" event.
     */
    public function updated(BookInventory $inventory)
    {

        $category = NotificationCategory::where('slug','book_inventory_stock')->first();

        if (! $category) {
            return;
        }


        // فقط وقتی quantity تغییر کرده
        if (! $inventory->wasChanged('quantity')) {
            return;
        }


        // اگر موجودی بیشتر از حد مجاز است
        if ($inventory->quantity > $inventory->book->minimum_stock) {
            return;
        }



       $users = User::where(function ($query) use ($inventory, $category) {

            // کاربران مرکزی
            $query->where(function ($q) use ($category) {

                $q->whereNull('branch_id')
                    ->whereHas('role.notificationCategories', function ($role) use ($category) {

                        $role->where('notification_categories.id', $category->id);

                    });

            });

            // کاربران شعبه
            $query->orWhere(function ($q) use ($inventory, $category) {

                $q->where('branch_id', $inventory->warehouse->branch_id)
                    ->whereHas('role.notificationCategories', function ($role) use ($category) {

                        $role->where('notification_categories.id', $category->id);

                    });

            });

        })->get();



        foreach ($users as $user) {


            $exists = $user->notifications()
                ->where(
                    'data->type',
                    'book_inventory_stock'
                )
                ->where(
                    'data->reference_id',
                    $inventory->id
                )
                ->whereNull('read_at')
                ->exists();



            if (! $exists) {

                $user->notify(
                    new BookInventoryStockNotification($inventory)
                );

            }

        }

    }

    /**
     * Handle the BookInventory "deleted" event.
     */
    public function deleted(BookInventory $bookInventory): void
    {
        //
    }

    /**
     * Handle the BookInventory "restored" event.
     */
    public function restored(BookInventory $bookInventory): void
    {
        //
    }

    /**
     * Handle the BookInventory "force deleted" event.
     */
    public function forceDeleted(BookInventory $bookInventory): void
    {
        //
    }
}
