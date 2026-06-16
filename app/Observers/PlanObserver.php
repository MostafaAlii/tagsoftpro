<?php

namespace App\Observers;

use App\Models\Plan;

class PlanObserver
{
    public function creating(Plan $plan): void
    {
        $user = get_user_data();
        $plan->company_id = $plan->company_id ?? $user?->company_id;
        $plan->created_by = $user?->id;
    }

    public function updating(Plan $plan): void
    {
        $plan->updated_by = get_user_data()?->id;
    }

    // لو الـ plan اتغيرت features بتاعتها → sync على كل الـ companies المشتركة
    public function updated(Plan $plan): void
    {
        if ($plan->isDirty('status')) {
            return; // status change مش هيأثر على الـ companies
        }
    }
}