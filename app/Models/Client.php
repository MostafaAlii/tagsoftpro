<?php

namespace App\Models;

use App\Enums\Client\ClientStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Client extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'clients';
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => ClientStatus::class,
    ];

    public function setPasswordAttribute($value) {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function getStatusHtmlAttribute(): string
    {
        return ClientStatus::status($this->status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ClientStatus::ACTIVE->value);
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', ClientStatus::BLOCKED->value);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ClientStatus::IN_ACTIVE->value);
    }

    public function companies() {
        return $this->hasMany(Company::class);
    }
}