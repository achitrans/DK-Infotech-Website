<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Estimate;
use App\Models\Product;

class EstimateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'estimate_id',
        'item_name',
        'hsn_code',
        'quantity',
        'rate',
        'discount',
        'taxable_value',
        'gst_rate',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'float',
        'rate' => 'float',
        'discount' => 'float',
        'taxable_value' => 'float',
        'gst_rate' => 'float',
        'total_amount' => 'float',
    ];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
