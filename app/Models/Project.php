<?php

declare(strict_types=1);

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Enums\Project\ProjectStatus;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use App\Models\Concerns\UploadMedia;
class Project extends BaseModel implements TranslatableContract
{
    use Translatable, UploadMedia;

    protected $table = 'projects';

    protected $fillable = [
        'uuid',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'status' => ProjectStatus::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void
    {

        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', ProjectStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ProjectStatus::INACTIVE);
    }

    public function scopePublished($query)
    {
        return $query->where('status', ProjectStatus::PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', ProjectStatus::DRAFT);
    }

    // ─── Relationships ──────────────────────────────────────

    // ✅ Many-to-Many مع Project Types
    public function projectTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class, 'project_project_types')
            ->withTimestamps();
    }

    // ✅ Many-to-Many مع Modules
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'project_modules')
            ->withTimestamps();
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

    public function getProjectTypesNames(): string
    {
        return $this->projectTypes->map(fn($pt) => $pt->getTranslatedName())->implode(', ') ?: '-';
    }

    public function getModulesNames(): string
    {
        return $this->modules->map(fn($module) => $module->getTranslatedName())->implode(', ') ?: '-';
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}