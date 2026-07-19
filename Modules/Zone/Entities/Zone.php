<?php

namespace Modules\Zone\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Zone\Enums\ZoneStatus;
use Modules\Zone\Entities\Translations\ZoneTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Zone extends Model implements TranslatableContract {
    use SoftDeletes, Translatable;

    protected $table = 'zones';
    public $translationModel = ZoneTranslation::class;

    protected $fillable = [
        'key',
        'status',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'status' => ZoneStatus::class,
    ];

    // ─── Relationships ──────────────────────────────────────────────
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.admins.model', \App\Models\Admin::class), 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.admins.model', \App\Models\Admin::class), 'updated_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', ZoneStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ZoneStatus::INACTIVE);
    }

    // ─── Methods ──────────────────────────────────────────────────────
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