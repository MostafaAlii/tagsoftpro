<?php

declare(strict_types=1);

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Enums\ProjectType\ProjectTypeStatus;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class ProjectType extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'project_types';
    protected $fillable = [
        'uuid',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'status' => ProjectTypeStatus::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', ProjectTypeStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ProjectTypeStatus::INACTIVE);
    }

    // ─── Relationships ──────────────────────────────────────
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'module_project_types')
            ->withTimestamps();
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function themes(): BelongsToMany {
        return $this->belongsToMany(Theme::class, 'project_type_theme')->withPivot('is_active', 'is_default', 'created_by', 'updated_by')->withTimestamps();
    }

    public function activeThemes(): BelongsToMany {
        return $this->themes()->wherePivot('is_active', true);
    }

    public function defaultTheme(): ?Theme {
        return $this->themes()->wherePivot('is_default', true)->first();
    }

    // ─── Methods ──────────────────────────────────────────────
    public function getTranslatedName(): string
    {
        return $this->translate(app()->getLocale())?->name
            ?? $this->translate('ar')?->name
            ?? $this->name
            ?? '-';
    }

    public function getTranslatedDescription(): ?string
    {
        return $this->translate(app()->getLocale())?->description
            ?? $this->translate('ar')?->description;
    }

    public function hasModule($moduleId): bool
    {
        return $this->modules()->where('module_id', $moduleId)->exists();
    }

    public function getModulesNames(): string
    {
        return $this->modules->map(function ($module) {
            return $module->getTranslatedName();
        })->implode(', ');
    }
}