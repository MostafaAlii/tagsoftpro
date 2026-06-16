<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Admin\{AdminType,AdminStatus};
use Illuminate\Database\Eloquent\Relations\HasOne;

class Admin extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'admins';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'type',
        'password',
        'date',
        'company_id',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date' => 'date',
        'status' => AdminStatus::class,
        'type' => AdminType::class,
    ];

    public function profile(): HasOne {
        return $this->hasOne(related: AdminProfile::class, foreignKey: 'admin_id');
    }

    public function scopeActive() {
        return $this->whereStatus('active')->get();
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    /*public function branch() {
        return $this->belongsTo(Branch::class);
    }*/
    
}