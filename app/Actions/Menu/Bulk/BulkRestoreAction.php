<?php

namespace App\Actions\Menu\Bulk;

use App\Actions\Menu\Bulk\Contracts\BulkActionInterface;
use App\Models\Menu;

class BulkRestoreAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        Menu::withTrashed()->whereIn('id', $ids)->restore();
        return trans('dashboard/menus.bulk_restored_successfully');
    }
}