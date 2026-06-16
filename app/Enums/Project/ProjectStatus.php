<?php

namespace App\Enums\Project;

enum ProjectStatus: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case PUBLISHED = 'published';
    case DRAFT    = 'draft';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE    => trans('dashboard/projects.status_active'),
            self::INACTIVE  => trans('dashboard/projects.status_inactive'),
            self::PUBLISHED => trans('dashboard/projects.status_published'),
            self::DRAFT     => trans('dashboard/projects.status_draft'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVE    => '<span class="badge bg-success">' . $this->label() . '</span>',
            self::INACTIVE  => '<span class="badge bg-danger">' . $this->label() . '</span>',
            self::PUBLISHED => '<span class="badge bg-primary">' . $this->label() . '</span>',
            self::DRAFT     => '<span class="badge bg-warning">' . $this->label() . '</span>',
        };
    }
}