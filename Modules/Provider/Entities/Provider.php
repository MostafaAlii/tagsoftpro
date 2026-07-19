<?php

namespace Modules\Provider\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Provider\Enums\ProviderStatus;
use Modules\Zone\Entities\Zone;

class Provider extends Model
{
    use SoftDeletes;

    protected $table = 'providers';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'phone',
        'status',
        'password',
        'date',
        'address',
        'website',
        'zone_id',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date' => 'date',
        'status' => ProviderStatus::class,
    ];

    // ─── Relationships ──────────────────────────────────────────────
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

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
        return $query->where('status', ProviderStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ProviderStatus::INACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', ProviderStatus::PENDING);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', ProviderStatus::SUSPENDED);
    }

    // ─── Accessors ──────────────────────────────────────────────────
    public function getStatusBadgeAttribute(): string
    {
        return $this->status->badge();
    }
}