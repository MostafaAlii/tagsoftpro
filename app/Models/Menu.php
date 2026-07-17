<?php

namespace App\Models;

use App\Enums\Menu\MenuStatus;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Translations\MenuTranslation;
class Menu extends BaseModel implements TranslatableContract {
    use Translatable, SoftDeletes;
    protected $table = 'menus';
    public $translationModel = MenuTranslation::class;
    protected $fillable = [
        'uuid',
        'key',
        'route_prefix',
        'icon',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = ['name', 'description'];
    protected $casts = [
        'status' => MenuStatus::class,
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query) {
        return $query->where('status', MenuStatus::ACTIVE);
    }

    public function scopeInactive($query) {
        return $query->where('status', MenuStatus::INACTIVE);
    }

    // ─── Relationships ──────────────────────────────────────
    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo {
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
}
