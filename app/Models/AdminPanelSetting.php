<?php
namespace App\Models;
use App\Enums\MainSetting\{MainSettingSystemStatus};
use App\Models\Concerns\UploadMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AdminPanelSetting extends BaseModel {
    use UploadMedia;
    protected $table = 'admin_panel_settings';
    protected $fillable = [
        'uuid',
        'system_status',
        'company_name',
        'company_id',
        'phone',
        'address',
        'email',
        'added_by_id',
        'updated_by_id',
        'company_id',
        'theme_id',
    ];

    protected $casts = [
        'system_status' => MainSettingSystemStatus::class,
    ];

    public function theme(): BelongsTo {
        return $this->belongsTo(Theme::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}