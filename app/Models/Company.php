<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Company\{CompanyStatus};
use App\Enums\CompanyFeature\CompanyFeatureStatus;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany,BelongsTo};
class Company extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'companies';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'project_type_id',
        'password',
        'client_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date' => 'date',
        'status' => CompanyStatus::class,
    ];

    protected static function booted() {
        static::addGlobalScope('company_visibility', function ($builder) {
            $user = get_user_data();
            if ($user?->type !== \App\Enums\Admin\AdminType::OWNER || !is_null($user?->company_id)) {
                $builder->where('id', $user?->company_id);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', CompanyStatus::ACTIVE);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'company_features')
            ->withPivot('is_active', 'expires_at')
            ->withTimestamps();
    }

    public function activeFeatures(): BelongsToMany
    {
        return $this->features()
            ->wherePivot('is_active', true)
            ->where(fn($q) => $q->whereNull('company_features.expires_at')
                ->orWhere('company_features.expires_at', '>', now()));
    }

    public function hasFeature(string $featureKey): bool
    {
        return $this->activeFeatures()
            ->whereHas('translations', fn($q) => $q->where('key', $featureKey))
            ->exists();
    }

    public function assignPlan(Plan $plan): void
    {
        $this->update([
            'plan_id'          => $plan->id,
            'plan_expires_at'  => now()->addMonths($plan->billing_cycle->months()),
        ]);

        $plan->syncToCompany($this);
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }
}
