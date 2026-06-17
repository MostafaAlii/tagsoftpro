<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class EmployeeProfile extends BaseModel
{
    protected $table = 'employee_profiles';
    protected $fillable = ['bio', 'employee_id', 'uuid', 'address'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(related: Employee::class, foreignKey: 'employee_id');
    }
}
