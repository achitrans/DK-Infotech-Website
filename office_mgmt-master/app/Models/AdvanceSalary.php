<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvanceSalary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'requested_by',
        'approved_by',
        'amount',
        'outstanding_amount',
        'term_type',
        'deduction_value',
        'status',
        'remarks',
        'reference',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'deduction_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SETTLED = 'settled';

    public const TERM_FULL = 'full';
    public const TERM_FIXED = 'fixed_amount';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeOutstanding($query)
    {
        return $query->where('outstanding_amount', '>', 0);
    }

    public function applyDeduction(float $amount): float
    {
        $amount = min($amount, $this->outstanding_amount);
        $this->outstanding_amount = max(0, $this->outstanding_amount - $amount);
        if ($this->outstanding_amount <= 0) {
            $this->status = self::STATUS_SETTLED;
        }
        $this->save();
        return $amount;
    }
}
