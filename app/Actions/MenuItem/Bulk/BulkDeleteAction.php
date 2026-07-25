<?php

namespace App\Actions\MenuItem\Bulk;

use App\Actions\MenuItem\Bulk\Contracts\BulkActionInterface;
use App\Models\MenuItem;

class BulkDeleteAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        MenuItem::whereIn('id', $ids)->delete();
        return trans('dashboard/menu_items.bulk_deleted');
    }
}