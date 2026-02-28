<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class AccessRole extends Model
{
    protected $fillable = ['role_name'];


    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         User::class,          // مدل مرتبط
    //         'access_role_user',   // جدول pivot
    //         'role_id',            // کلید خارجی مدل جاری
    //         'user_id'             // کلید خارجی مدل مرتبط
    //     )->withTimestamps();
    // }

    protected static function booted()
    {
        parent::booted();

        static::deleting(function ($role) {
            if ($role->is_system) { // Developer
                throw new \Exception('System role cannot be deleted.');
            }

            if ($role->name === 'Super Admin') {
                throw new \Exception('Super Admin role cannot be deleted.');
            }
        });
    }
}
