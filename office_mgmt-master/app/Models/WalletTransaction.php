<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'reference',
        'balance_before',
        'balance_after',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createEntry($userId, $amount, $description, $reference = null)
    {
        $currentBalance = self::where('user_id', $userId)->sum('amount');

        return self::create([
            'user_id' => $userId,
            'amount' => $amount,
            'description' => $description,
            'reference' => $reference,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $amount,
            'transaction_date' => now(),
        ]);
    }
}
