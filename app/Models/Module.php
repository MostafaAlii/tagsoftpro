<?php

declare(strict_types=1);

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Enums\Module\ModuleStatus;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class Module extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'modules';
    protected $fillable = [
        'uuid',
        'status',
        'company_id',
        'project_type_id',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'status' => ModuleStatus::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', ModuleStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ModuleStatus::INACTIVE);
    }

    // ─── Relationships ──────────────────────────────────────
    public function projectTypes(): BelongsToMany {
        return $this->belongsToMany(ProjectType::class, 'module_project_types')->withTimestamps();
    }
    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // ─── Methods ──────────────────────────────────────────────
    public function getTranslatedName(): string {
        return $this->translate(app()->getLocale())?->name
            ?? $this->translate('ar')?->name
            ?? $this->name
            ?? '-';
    }

    public function getTranslatedDescription(): ?string {
        return $this->translate(app()->getLocale())?->description
            ?? $this->translate('ar')?->description;
    }

    public function hasProjectType($projectTypeId): bool {
        return $this->projectTypes()->where('project_type_id', $projectTypeId)->exists();
    }

    public function getProjectTypesNames(): string {
        return $this->projectTypes->map(function ($pt) {
            return $pt->getTranslatedName();
        })->implode(', ');
    }
}