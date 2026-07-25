<?php
namespace App\Models\Translations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MenuItem;
class MenuItemTranslation extends Model {
    protected $table = 'menu_item_translations';
    protected $fillable = [
        'menu_item_id',
        'locale',
        'title',
        'description',
    ];
    public $timestamps = false;
    public function menuItem(): BelongsTo {
        return $this->belongsTo(MenuItem::class);
    }
}