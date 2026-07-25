<?php
namespace App\Models;
use App\Enums\MenuItem\{MenuItemStatus,MenuItemType,MenuItemLinkType,MenuItemTarget};
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Translations\MenuItemTranslation;
class MenuItem extends BaseModel implements TranslatableContract {
    use Translatable, SoftDeletes;
    protected $table = 'menu_items';
    protected $fillable = [
        'uuid',
        'type',
        'icon',
        'link_type',
        'route_name',
        'route_params',
        'url',
        'target',
        'is_owner_only',
        'permission_name',
        'badge_text',
        'badge_color',
        'status',
        'visible_from',
        'visible_until',
        'company_id',
        'created_by',
        'updated_by',
    ];
    public $translatedAttributes = ['title', 'description'];
    protected $casts = [
        'type' => MenuItemType::class,
        'link_type' => MenuItemLinkType::class,
        'target' => MenuItemTarget::class,
        'status' => MenuItemStatus::class,
        'route_params' => 'array',
        'is_owner_only' => 'boolean',
        'visible_from' => 'datetime',
        'visible_until' => 'datetime',
    ];

    // ─── Boot ──────────────────────────────────────────────
    protected static function booted(): void {
        static::addGlobalScope(new Scopes\CompanyScope());
    }

    // ─── Scopes ──────────────────────────────────────────────
    public function scopeActive($query) {
        return $query->where('status', MenuItemStatus::ACTIVE);
    }

    public function scopeInactive($query) {
        return $query->where('status', MenuItemStatus::INACTIVE);
    }

    public function scopeType($query, $type) {
        return $query->where('type', $type);
    }

    public function scopeVisible($query) {
        return $query->where(function ($q) {
            $q->whereNull('visible_from')->orWhere('visible_from', '<=', now());
        })->where(function ($q) {
            $q->whereNull('visible_until')->orWhere('visible_until', '>=', now());
        });
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

    // ─── Translations Relationship ──────────────────────────
    public function translations(): HasMany {
        return $this->hasMany(MenuItemTranslation::class);
    }

    // ─── Methods ──────────────────────────────────────────────
    public function getTranslatedTitle(): string {
        return $this->translate(app()->getLocale())?->title
            ?? $this->translate('ar')?->title
            ?? $this->title
            ?? '-';
    }

    public function getTranslatedDescription(): ?string {
        return $this->translate(app()->getLocale())?->description ?? $this->translate('ar')?->description;
    }

    public function getLink(): ?string {
        return match ($this->link_type) {
            'route' => $this->route_name ? route($this->route_name, $this->route_params ?? []) : null,
            'url' => $this->url,
            default => null,
        };
    }
}