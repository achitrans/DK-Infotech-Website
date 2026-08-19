<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordVault extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'password',
        'url',
        'notes',
        'category',
        'last_used_at',
        'is_shared',
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function audits()
    {
        return $this->hasMany(PasswordVaultAudit::class, 'vault_id');
    }
}