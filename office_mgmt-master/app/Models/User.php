<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public static $departments = [
        'development' => 'Development',
        'intern' => 'Intern',
        'digital marketing' => 'Digital Marketing',
        'sales' => 'Sales',
        'admin' => 'Admin',
        'associate' => 'Associate',
        'hr' => 'HR',
        'client' => 'Client',
        'executive' => 'Executive',
        'team lead' => 'Team Lead',
        'manager' => 'Manager',
        'assistant manager' => 'Assistant Manager',
        'accounts' => 'Accounts',
        'management' => 'Management',
    ];

    public static $types = [
        'employee' => 'Employee',
        'intern' => 'Inten',
        'client' => 'Client',
        'associate' => 'Associate',
        'admin' => 'Admin',
        'accounts' => 'Accounts',
        'branch manager' => 'Branch Manager',
    ];

    public static $workLocations = [
        'office' => 'Office',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
        'temporary remote' => 'Temporary Remote',
    ];

    public static $status = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];

    public static $positions = [
        'Digital Marketing Executive',
        'Digital Marketing Manager',
        'Software Developer',
        'Senior Software Developer',
        'HR',
        'Sales Executive',
        'Sales Manager',
        'Intern',
        'Telecaller',
        'Executive',
        'Team Lead',
        'Project Manager',
        'Manager',
        'Assistant Manager',
        'Associate',
        'Accounts',
        'Branch Manager',
    ];

    public function salary()
    {
        return $this->hasOne(UserSalary::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'department',
        'type',
        'work_location',
        'email_verified_at',
        'employee_id',
        'status',
        'barcode_rfid',
        'position',
        'parent_id',
        'created_by',
        'branch_id',
        'tawk_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'department' => 'string',
            'type' => 'string',
        ];
    }

    public function kyc()
    {
        return $this->hasOne(UserKyc::class);
    }

    public function kycClient()
    {
        return $this->hasOne(ClientKyc::class);
    }

    public function experienceLetters()
    {
        return $this->hasOne(ExperienceLetter::class);
    }

    public function career()
    {
        return $this->hasOne(Career::class);
    }

    public function internship()
    {
        return $this->hasOne(InternshipInterest::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function geoLocationLogs()
    {
        return $this->hasMany(UserGeoLocationLog::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeClients($query)
    {
        return $query->where('type', 'client');
    }

    public function scopeEmployees($query)
    {
        return $query->whereNotIn('type', ['client', 'admin']);
    }

    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    public function isEmployee()
    {
        return $this->type === 'employee' || $this->type === 'intern';
    }

    public function isClient()
    {
        return $this->type === 'client';
    }

    public function isAssociate()
    {
        return $this->type === 'associate';
    }

    public function isBranchManager()
    {
        return $this->type === 'branch manager';
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->type, $roles);
    }

    public function hasAnyDept(array $department): bool
    {
        return in_array($this->department, $department);
    }

    public static function generateEmployeeId()
    {
        $prefix = env('EMPLOYEE_ID_PREFIX', 'EMP');
        $lastUser = self::orderBy('id', 'desc')->where('type', 'employee')->first();
        $lastId = $lastUser ? (int) substr($lastUser->employee_id, strlen($prefix)) : 0;
        $newId = $lastId + 1;

        return $prefix.str_pad($newId, 4, '0', STR_PAD_LEFT);
    }

    public static function generateInternId()
    {
        $prefix = env('INTERN_ID_PREFIX', 'INT');
        $lastUser = self::orderBy('id', 'desc')->where('type', 'intern')->first();
        $lastId = $lastUser ? (int) substr($lastUser->employee_id, strlen($prefix)) : 0;
        $newId = $lastId + 1;

        return $prefix.str_pad($newId, 4, '0', STR_PAD_LEFT);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getWalletBalance()
    {
        return $this->walletTransactions()->sum('amount');
    }
}
