<?php
declare(strict_types=1);
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Enums\Plan\{PlanStatus,PlanBillingCycle};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany,HasMany};
class Plan extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'plans';
    protected $fillable = [
        'uuid',
        'price',
        'billing_cycle',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];
    public $translatedAttributes = ['name', 'description'];
    protected $casts = [
        'status'        => PlanStatus::class,
        'billing_cycle' => PlanBillingCycle::class,
        'price'         => 'decimal:2',
    ];

    // Scopes
    public function scopeActive($query) {
        return $query->where('status', PlanStatus::ACTIVE);
    }

    public function scopeInactive($query) {
        return $query->where('status', PlanStatus::INACTIVE);
    }

    // Relationships
    public function features(): BelongsToMany {
        return $this->belongsToMany(Feature::class, 'plan_features')->withPivot('is_included', 'limit')->withTimestamps();
    }

    public function includedFeatures(): BelongsToMany {
        return $this->features()->wherePivot('is_included', true);
    }

    public function companies(): HasMany {
        return $this->hasMany(Company::class);
    }

    public function createdBy() {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy() {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Methods
    public function syncToCompany(Company $company): void {
        $features = $this->includedFeatures()->get();
        $sync = $features->mapWithKeys(fn($feature) => [
            $feature->id => [
                'is_active'  => true,
                'expires_at' => now()->addMonths($this->billing_cycle->months()),
            ]
        ])->toArray();
        $company->features()->sync($sync);
    }

    protected static function booted(): void {
        static::addGlobalScope(new Scopes\CompanyScope());
    }
}