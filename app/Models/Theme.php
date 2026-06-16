<?php

namespace App\Models;

use App\Enums\Theme\ThemeDefault;
use App\Enums\Theme\ThemeStatus;

class Theme extends BaseModel
{
    protected $fillable = [
        'uuid',
        'name',
        'code',
        'description',
        'is_active',
        'is_default',
        'company_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'  => ThemeStatus::class,
        'is_default' => ThemeDefault::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function adminPanelSettings() {
        return $this->hasMany(AdminPanelSetting::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDefault(): bool
    {
        return $this->is_default === ThemeDefault::YES;
    }

    public function isActive(): bool
    {
        return $this->is_active === ThemeStatus::ACTIVE;
    }
}