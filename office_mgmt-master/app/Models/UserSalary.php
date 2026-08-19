<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserSalary extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'branch_id', 'basic', 'hra', 'conveyance', 'special_allowance', 'medical_allowance', 'other_allowance', 'gross_salary', 'pf', 'esi', 'professional_tax', 'tds', 'effective_from', 'effective_to'
    ];
    public function user() { return $this->belongsTo(User::class); }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
