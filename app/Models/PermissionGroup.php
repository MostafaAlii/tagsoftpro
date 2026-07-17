<?php

namespace App\Models;

use App\Enums\PermissionGroup\PermissionGroupStatus;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class PermissionGroup extends BaseModel implements TranslatableContract {
    use Translatable, SoftDeletes;
    protected $table = 'permission_groups';
    protected $fillable = [
        'uuid',
        'icon',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'status' => PermissionGroupStatus::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', PermissionGroupStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', PermissionGroupStatus::INACTIVE);
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