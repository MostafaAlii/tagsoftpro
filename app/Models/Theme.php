<?php

namespace App\Models;

use App\Enums\Theme\ThemePaidType;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class Theme extends BaseModel {
    protected $fillable = [
        'uuid',
        'name',
        'code',
        'description',
        'paid_type',
        'price',
        'company_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'paid_type' => ThemePaidType::class,
        'price' => 'decimal:2',
    ];

    // ─── Relationships ──────────────────────────────────────
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function projectTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class, 'project_type_theme')
            ->withPivot('is_active', 'is_default', 'created_by', 'updated_by')
            ->withTimestamps();
    }

    public function activeProjectTypes(): BelongsToMany
    {
        return $this->projectTypes()->wherePivot('is_active', true);
    }

    public function defaultProjectTypes(): BelongsToMany
    {
        return $this->projectTypes()->wherePivot('is_default', true);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // ─── Helpers ──────────────────────────────────────────────
    public function isDefault(): bool
    {
        return $this->defaultProjectTypes()->exists();
    }
    
    public function isActiveForProjectType(int $projectTypeId): bool
    {
        return $this->projectTypes()
            ->where('project_type_id', $projectTypeId)
            ->wherePivot('is_active', true)
            ->exists();
    }

    public function isDefaultForProjectType(int $projectTypeId): bool
    {
        return $this->projectTypes()
            ->where('project_type_id', $projectTypeId)
            ->wherePivot('is_default', true)
            ->exists();
    }

    public function getStatusForProjectType(int $projectTypeId): ?bool
    {
        $pivot = $this->projectTypes()
            ->where('project_type_id', $projectTypeId)
            ->first()?->pivot;
            
        return $pivot?->is_active;
    }

    public function getPaidTypeBadge(): string
    {
        return $this->paid_type->badge();
    }

    public function getFormattedPrice(): string
    {
        if ($this->paid_type === ThemePaidType::FREE) {
            return trans('dashboard/themes.free');
        }
        return number_format($this->price, 2) . ' ' . (config('app.currency') ?? 'EGP');
    }
}