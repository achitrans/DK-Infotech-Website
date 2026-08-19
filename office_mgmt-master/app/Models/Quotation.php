<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'name',
        'mobile',
        'email',
        'branch_id',
        'intro',
        'description',
        'terms',
        'date',
        'exp_date',
    ];

    protected $dates = [
        'date',
        'exp_date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
