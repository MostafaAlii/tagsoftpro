<?php

namespace Modules\Zone\Entities\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Zone\Entities\Zone;

class ZoneTranslation extends Model
{
    protected $table = 'zone_translations';

    protected $fillable = [
        'zone_id',
        'locale',
        'name',
        'description',
    ];

    public $timestamps = false;

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}