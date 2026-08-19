<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Estimate extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'client_id',
        'buyer_name',
        'buyer_mobile',
        'buyer_gstin',
        'estimate_number',
        'estimate_date',
        'expiry_date',
        'sub_total',
        'total_tax',
        'grand_total',
        'status',
        'converted_invoice_id',
        'created_by',
        'branch_id',
    ];

    protected $casts = [
        'estimate_date' => 'date',
        'expiry_date' => 'date',
        'sub_total' => 'float',
        'total_tax' => 'float',
        'grand_total' => 'float',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function convertedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id')->withTrashed();
    }

    public function getPublicTokenAttribute(): string
    {
        return Crypt::encryptString((string) $this->id);
    }

    public static function nextEstimateNumber(string $resetPeriod = 'daily'): string
    {
        $today = now();
        $query = self::query();
        $resetPeriod = strtolower($resetPeriod);

        $prefixFormat = match ($resetPeriod) {
            'monthly' => 'Ym',
            'yearly' => 'Y',
            default => 'Ymd',
        };

        if ($resetPeriod === 'monthly') {
            $query->whereYear('estimate_date', $today->year)
                ->whereMonth('estimate_date', $today->month);
        } elseif ($resetPeriod === 'yearly') {
            $query->whereYear('estimate_date', $today->year);
        } else {
            $query->whereDate('estimate_date', $today);
        }

        $latest = $query->orderByDesc('id')->first();
        $nextSequence = 0;

        if ($latest && $latest->estimate_number) {
            $lastSegment = Str::afterLast($latest->estimate_number, '-');
            if (is_numeric($lastSegment)) {
                $nextSequence = (int) $lastSegment + 1;
            }
        }else{
            $nextSequence = 1;
        }

        return sprintf('EST-%s-%04d', $today->format($prefixFormat), $nextSequence);
    }
}
