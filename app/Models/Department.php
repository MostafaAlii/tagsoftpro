<?php

declare(strict_types=1);

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Enums\Department\DepartmentStatus;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Department extends BaseModel implements TranslatableContract
{
    use Translatable;

    protected $table = 'departments';

    protected $fillable = [
        'uuid',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'status' => DepartmentStatus::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', DepartmentStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', DepartmentStatus::INACTIVE);
    }

    // ─── Relationships ──────────────────────────────────────
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
}