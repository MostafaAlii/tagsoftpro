<?php

namespace App\Actions\Menu\Bulk;

use App\Actions\Menu\Bulk\Contracts\BulkActionInterface;
use App\Models\Menu;

class BulkForceDeleteAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        Menu::withTrashed()->whereIn('id', $ids)->forceDelete();
        return trans('dashboard/menus.bulk_force_deleted_successfully');
    }
}