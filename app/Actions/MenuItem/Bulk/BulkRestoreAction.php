<?php

namespace App\Actions\MenuItem\Bulk;

use App\Actions\MenuItem\Bulk\Contracts\BulkActionInterface;
use App\Models\MenuItem;

class BulkRestoreAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        MenuItem::withTrashed()->whereIn('id', $ids)->restore();
        return trans('dashboard/menu_items.bulk_restored_successfully');
    }
}