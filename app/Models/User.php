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
use App\Models\Settings\Country;
use App\Models\Settings\Province;
use App\Models\Settings\EducationDegree;
use App\Models\Settings\AcademicRank;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'name_fa',
        'name_en',
        'family_name_fa',
        'family_name_en',
        'phone_no',
        'affiliation_fa',
        'affiliation_en',
        'country_id',
        'province_id',
        'department_fa',
        'department_en',
        'preferred_research_area_fa',
        'preferred_research_area_en',
        'city_fa',
        'city_en',
        'education_degree_id',
        'academic_rank_id',
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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessRole::class,    // مدل مرتبط
            'access_role_user',   // جدول pivot
            'user_id',            // کلید خارجی مدل جاری
            'role_id'             // کلید خارجی مدل مرتبط
        )->withTimestamps();      // اگر جدول pivot timestamps دارد
    }
    public function getRoleIdsAttribute()
    {
        return $this->roles->pluck('id')->toArray();
    }
    public function isAdmin()
    {
        return $this->roles->contains('role_name', 'Admin');
    }

    public function isReviewer()
    {
        return $this->roles->count() === 1 
            && $this->roles->contains('role_name', 'Reviewer');
    }
    public function isAuthor()
    {
        return $this->roles->count() === 1 
            && $this->roles->contains('role_name', 'Author');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
    public function educationDegree()
    {
        return $this->belongsTo(EducationDegree::class, 'academic_rank_id');
    }
    public function academicRank()
    {
        return $this->belongsTo(AcademicRank::class, 'academic_rank_id');
    }
}
