<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\Hr\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class EmployeeBookMovement extends Model
{
     protected $fillable = [
        'employee_id',
        'book_inventory_id',
        'book_id',
        'quantity',
        'type',
        'movement_date',
        'note',
        'user_id',

        'return_date',
        'return_note',
        'returned_by',
    ];

     protected $casts = [
        'movement_date' => 'date',
        'return_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bookInventory()
    {
        return $this->belongsTo(BookInventory::class, 'book_inventory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(PhysicalBook::class);
    }
}
