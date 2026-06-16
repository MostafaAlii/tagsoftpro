<?php
namespace App\Enums\Plan;
enum PlanBillingCycle: string {
    case MONTHLY = 'monthly';
    case YEARLY  = 'yearly';
    public function label(): string {
        return match ($this) {
            self::MONTHLY => trans('dashboard/plans.billing_monthly'),
            self::YEARLY  => trans('dashboard/plans.billing_yearly'),
        };
    }

    public function months(): int {
        return match ($this) {
            self::MONTHLY => 1,
            self::YEARLY  => 12,
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::MONTHLY   => '<span class="badge bg-success">' . $this->label() . '</span>',
            self::YEARLY => '<span class="badge bg-info">'  . $this->label() . '</span>',
        };
    }
}