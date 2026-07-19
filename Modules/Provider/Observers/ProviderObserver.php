<?php

namespace Modules\Provider\Observers;

use Modules\Provider\Entities\Provider;

class ProviderObserver
{
    public function creating(Provider $provider): void
    {
        $user = get_user_data();
        $provider->created_by = $user?->id;
    }

    public function updating(Provider $provider): void
    {
        $user = get_user_data();
        $provider->updated_by = $user?->id;
    }

    public function restoring(Provider $provider): void
    {
        $user = get_user_data();
        $provider->updated_by = $user?->id;
    }
}