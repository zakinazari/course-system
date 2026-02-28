<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EmployeeFile extends Model
{
      protected $fillable = [
        'employee_id',
        'file_type',
        'file_name',
        'file_path',
        'thumbnail_path',
        'mime_type',
        'file_size',
        'disk',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size'   => 'integer',
    ];

    const TYPE_PHOTO     = 'photo';
    const TYPE_TAZKIRA   = 'tazkira';
    const TYPE_PASSPORT  = 'passport';
    const TYPE_TRANSCRIPT = 'transcript';
    const TYPE_OTHER     = 'other';


    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk($this->disk)
            ->url($this->thumbnail_path);
    }

    public function photo()
    {
        return $this->hasOne(EmployeeFile::class)
                    ->where('file_type', EmployeeFile::TYPE_PHOTO)
                    ->latest();
    }
    
}
