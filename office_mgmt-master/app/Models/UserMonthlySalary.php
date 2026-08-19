<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMonthlySalary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_monthly_salaries';

    protected $fillable = [
        'user_id',
        'branch_id',
        'salary_year',
        'salary_month',
        'salary_date',
        'basic',
        'hra',
        'conveyance',
        'special_allowance',
        'medical_allowance',
        'other_allowance',
        'gross_salary',
        'total_days',
        'present_days',
        'paid_leaves',
        'absent_days',
        'pf',
        'esi',
        'professional_tax',
        'tds',
        'lop_days',
        'lop_amount',
        'gross_deduction',
        'net_salary',
        'is_approved',
        'approved_at',
        'approved_by',
        'payment_status',
        'payment_date',
        'remarks',
        'payment_details',
        'advance_deductions',
        'advance_total_deduction',
    ];

    protected $casts = [
        'salary_date' => 'date',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
        'payment_details' => 'array',
        'advance_deductions' => 'array',
    ];

    public static array $payment_details_filed = [
        'payment_mode' => 'Payment Mode',
        'ref_no'       => 'Ref No'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getAdvanceDeductionSummaryAttribute()
    {
        return collect($this->advance_deductions ?? [])->sum('deducted_amount');
    }

}
