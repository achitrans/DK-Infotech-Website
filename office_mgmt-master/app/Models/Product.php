<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'hsn_code',
        'uom',
        'sales_price',
        'gst_rate',
        'is_service',
        'description',
        'html_description',
    ];

    protected $casts = [
        'sales_price' => 'float',
        'gst_rate' => 'float',
        'is_service' => 'boolean',
        'description' => 'string',
        'html_description' => 'string',
    ];

    public const UOM_OPTIONS = [
        'BOX',
        'KG',
        'LTR',
        'MTR',
        'PCS',
        'SET',
        'UNIT',
    ];

    public static function uomOptions(): array
    {
        return self::UOM_OPTIONS;
    }

    public const GST_RATE_OPTIONS = [
        0,
        0.5,
        1,
        5,
        12,
        18,
        28,
    ];

    public static function gstRateOptions(): array
    {
        return self::GST_RATE_OPTIONS;
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
