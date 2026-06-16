<?php
namespace App\Observers;
use App\Models\Feature;
class FeatureObserver {
    public function creating(Feature $feature): void
    {
        $user = get_user_data();
        $feature->company_id = $feature->company_id ?? $user?->company_id;
        $feature->created_by = $user?->id;
    }

    public function updating(Feature $feature): void
    {
        $feature->updated_by = get_user_data()?->id;
    }

    public function updated(Feature $feature): void {
        if ($feature->isDirty('status') && $feature->status === \App\Enums\Feature\FeatureStatus::INACTIVE) {
            $feature->companies()->updateExistingPivot(
                $feature->companies()->allRelatedIds(),
                ['is_active' => false]
            );
        }
    }
}