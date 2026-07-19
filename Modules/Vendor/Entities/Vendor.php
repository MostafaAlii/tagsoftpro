<?php
namespace Modules\Vendor\Entities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\{Company,Admin, Department};
class Vendor extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'vendors';
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'phone',
        'status',
        'type',
        'password',
        'date',
        'company_id',
        'department_id',
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
    ];

    // ─── Relationships ──────────────────────────────────────────────
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ─── Accessors ──────────────────────────────────────────────────
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'suspended' => 'secondary',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }
}