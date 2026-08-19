<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordVaultAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vault_id',
        'performed_by',
        'action',
        'description',
        'ip_address',
    ];

    public function vault()
    {
        return $this->belongsTo(PasswordVault::class, 'vault_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}