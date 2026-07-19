<?php

namespace Modules\Zone\Observers;

use Modules\Zone\Entities\Zone;
use Illuminate\Support\Str;

class ZoneObserver
{
    public function creating(Zone $zone): void
    {
        if (empty($zone->key)) {
            $zone->key = Str::slug($zone->translate('ar')?->name ?? 'zone', '_');
        }
        $user = get_user_data();
        $zone->created_by = $user?->id;
    }

    public function updating(Zone $zone): void
    {
        $user = get_user_data();
        $zone->updated_by = $user?->id;
    }

    public function restoring(Zone $zone): void
    {
        $user = get_user_data();
        $zone->updated_by = $user?->id;
    }
}