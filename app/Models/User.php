<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Settings\AccessRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
        'role_id',
        'branch_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // public function roles(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         AccessRole::class,    // مدل مرتبط
    //         'access_role_user',   // جدول pivot
    //         'user_id',            // کلید خارجی مدل جاری
    //         'role_id'             // کلید خارجی مدل مرتبط
    //     )->withTimestamps();      // اگر جدول pivot timestamps دارد
    // }

    // public function branches(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         Branch::class,    // مدل مرتبط
    //         'branch_user',   // جدول pivot
    //         'user_id',            // کلید خارجی مدل جاری
    //         'branch_id'             // کلید خارجی مدل مرتبط
    //     )->withTimestamps();      // اگر جدول pivot timestamps دارد
    // }

    public function role()
    {
        return $this->belongsTo(AccessRole::class, 'role_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    
    public function getRoleIdsAttribute()
    {
        return $this->role_id ? [$this->role_id] : [];
    }

    public function getBranchIdsAttribute()
    {
        return $this->branch_id ? [$this->branch_id] : [];
    }

    public function isDeveloper(): bool
    {
        return $this->role && $this->role->is_system;
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->role_name === 'Super Admin';
    }

    public function isRegularUser(): bool
    {
        return !$this->isDeveloper() && !$this->isAdmin();
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->isDeveloper()) {
                throw new \Exception('System Developer cannot be deleted.');
            }

            if ($user->isAdmin()) {
                throw new \Exception('Super Admin cannot be deleted.');
            }
        });

        // static::addGlobalScope('hide_system_users', function (Builder $builder) {
        //     $builder->whereHas('role', function ($q) {
        //         $q->where('is_system', false);
        //     });
        // });
    }

}
