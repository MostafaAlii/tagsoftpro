<?php
declare(strict_types=1);
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Enums\Feature\{FeatureStatus,FeatureType,FeatureScope};
use App\Models\Scopes;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany};
class Feature extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'features';
    protected string $translationModel = \App\Models\Translations\FeatureTranslation::class;
    protected $fillable = [
        'uuid',
        'type',
        'scope',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];
    public $translatedAttributes = ['name', 'key'];
    protected $casts = [
        'status' => FeatureStatus::class,
        'type'   => FeatureType::class,
        'scope'  => FeatureScope::class,
    ];

    // Scopes
    public function scopeActive($query) {
        return $query->where('status', FeatureStatus::ACTIVE);
    }

    public function scopeInactive($query) {
        return $query->where('status', FeatureStatus::INACTIVE);
    }

    public function scopeMain($query) {
        return $query->where('scope', FeatureScope::MAIN);
    }

    public function scopeAddon($query) {
        return $query->where('scope', FeatureScope::ADDON);
    }

    public function scopeOfType($query, FeatureType $type) {
        return $query->where('type', $type);
    }

    // Relationships
    public function plans(): BelongsToMany {
        return $this->belongsToMany(Plan::class, 'plan_features')->withPivot('is_included', 'limit')->withTimestamps();
    }

    public function companies(): BelongsToMany {
        return $this->belongsToMany(Company::class, 'company_features')->withPivot('is_active', 'expires_at')->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // Global Scope
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\CompanyScope());
    }
}