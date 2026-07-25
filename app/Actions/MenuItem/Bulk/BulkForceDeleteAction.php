<?php

namespace App\Actions\MenuItem\Bulk;

use App\Actions\MenuItem\Bulk\Contracts\BulkActionInterface;
use App\Models\MenuItem;

class BulkForceDeleteAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        MenuItem::withTrashed()->whereIn('id', $ids)->forceDelete();
        return trans('dashboard/menu_items.bulk_force_deleted_successfully');
    }
}