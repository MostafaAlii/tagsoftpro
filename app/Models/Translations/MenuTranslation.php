<?php
namespace App\Models\Translations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Menu;
class MenuTranslation extends Model {
    protected $table = 'menu_translations';
    protected $fillable = [
        'menu_id',
        'locale',
        'name',
        'description',
    ];
    public $timestamps = false;
    public function menu(): BelongsTo {
        return $this->belongsTo(Menu::class);
    }
}
