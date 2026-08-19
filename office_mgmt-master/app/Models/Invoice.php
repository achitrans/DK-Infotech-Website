<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'buyer_name',
        'buyer_mobile',
        'buyer_gstin',
        'invoice_number',
        'invoice_date',
        'billing_address',
        'shipping_address',
        'due_date',
        'sub_total',
        'total_cgst',
        'total_sgst',
        'total_igst',
        'grand_total',
        'status',
        'notes',
        'created_by',
        'branch_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sub_total' => 'float',
        'total_cgst' => 'float',
        'total_sgst' => 'float',
        'total_igst' => 'float',
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
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoicePayments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function convertedEstimate(): HasOne
    {
        return $this->hasOne(Estimate::class, 'converted_invoice_id')->withTrashed();
    }

    public function getPublicTokenAttribute(): string
    {
        return Crypt::encryptString((string) $this->id);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->invoicePayments()->sum('amount');
    }

    public function getAmountDueAttribute(): float
    {
        return max(0, $this->grand_total - $this->total_paid);
    }

    public static function nextInvoiceNumber(string $resetPeriod = 'daily'): string
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
            $query->whereYear('invoice_date', $today->year)
                ->whereMonth('invoice_date', $today->month);
        } elseif ($resetPeriod === 'yearly') {
            $query->whereYear('invoice_date', $today->year);
        } else {
            $query->whereDate('invoice_date', $today);
        }

        $latest = $query->orderByDesc('id')->first();
        $nextSequence = 0;

        if ($latest && $latest->invoice_number) {
            $lastSegment = Str::afterLast($latest->invoice_number, '-');
            if (is_numeric($lastSegment)) {
                $nextSequence = (int) $lastSegment + 1;
            }
        }else{
            $nextSequence = 1;
        }

        return sprintf('INV-%s-%04d', $today->format($prefixFormat), $nextSequence);
    }
}
