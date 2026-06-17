<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Employee\{EmployeeStatus, EmployeeType};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\UploadMedia;
class Employee extends Authenticatable {
    use HasFactory, Notifiable, SoftDeletes, UploadMedia;
    protected $table = 'employees';
    protected $fillable = [
        'name',
        'email',
        'department_id',
        'phone',
        'status',
        'type',
        'password',
        'date',
        'company_id',
        'created_by',
        'updated_by',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date' => 'date',
        'status' => EmployeeStatus::class,
        'type' => EmployeeType::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', EmployeeStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', EmployeeStatus::INACTIVE);
    }

    public function scopeFullTime($query)
    {
        return $query->where('type', EmployeeType::FULL_TIME);
    }

    public function scopePartTime($query)
    {
        return $query->where('type', EmployeeType::PART_TIME);
    }

    // ─── Relationships ──────────────────────────────────────
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(related: EmployeeProfile::class, foreignKey: 'employee_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getImageUrl(): ?string {
        return $this->getMediaUrl('employee', $this, null, 'media', 'employee');
    }

    // ─── Methods ──────────────────────────────────────────────
    public function getStatusBadge(): string
    {
        return $this->status->badge();
    }

    public function getTypeBadge(): string
    {
        return $this->type->badge();
    }

    public function getFullName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->status === EmployeeStatus::ACTIVE;
    }
}
