<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Unit;
use App\Models\User;
use App\Models\CenterSettings\Section;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Asset extends Model
{
    protected $fillable = [
        'branch_id',
        'section_id',
        'asset_category_id',
        'name',
        'asset_number',
        'code',
        'purchase_price',
        'unit_id',
        'quantity',
        'purchase_date',
        'note',
        'user_id',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'quantity' => 'integer',
        'purchase_date' => 'date',
    ];

      // ---------------- Branch ----------------
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ---------------- Section ----------------
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // ---------------- Category ----------------
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    // ---------------- Unit ----------------
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // ---------------- User (created by) ----------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        //  Global Scope شعبه
        static::addGlobalScope('branch', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            if ($user->isDeveloper() || $user->isAdmin()) {
                return;
            }
 
            $builder->where('branch_id', $user->branch_id);
        });

        // -------------Generating_code---------------------------------
        static::creating(function ($asset) {

            DB::transaction(function () use ($asset) {

                // گرفتن آخرین نمبر در همان branch + category
                $lastNumber = self::where('branch_id', $asset->branch_id)
                    ->where('asset_category_id', $asset->asset_category_id)
                    ->max('asset_number');

                $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

                $asset->asset_number = $nextNumber;

                // گرفتن code ها
                $branchCode = $asset->branch?->code ?? 'B00';
                $categoryCode = $asset->category?->code ?? 'CAT';

                $asset->code =
                    $branchCode . '-' .
                    strtoupper($categoryCode) . '-' .
                    str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            });
        });

        // ---------------- update ----------------
        static::updating(function ($asset) {

            // فقط اگر branch یا category تغییر کرده باشد
            if (
                $asset->isDirty('branch_id') ||
                $asset->isDirty('asset_category_id')
            ) {

                DB::transaction(function () use ($asset) {

                    $lastNumber = self::where('branch_id', $asset->branch_id)
                        ->where('asset_category_id', $asset->asset_category_id)
                        ->where('id', '!=', $asset->id)
                        ->max('asset_number');

                    $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

                    $asset->asset_number = $nextNumber;

                    $branchCode = $asset->branch?->code ?? 'B00';

                    $categoryCode = $asset->category?->code ?? 'CAT';

                    $asset->code =
                        $branchCode . '-' .
                        strtoupper($categoryCode) . '-' .
                        str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                });
            }
        });
    }
}
