<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class AccessRole extends Model
{
    protected $fillable = ['role_name'];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,          // مدل مرتبط
            'access_role_user',   // جدول pivot
            'role_id',            // کلید خارجی مدل جاری
            'user_id'             // کلید خارجی مدل مرتبط
        )->withTimestamps();
    }
}
