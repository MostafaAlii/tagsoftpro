<?php

namespace App\Actions\Menu\Bulk;

use App\Actions\Menu\Bulk\Contracts\BulkActionInterface;
use App\Models\Menu;

class BulkDeleteAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        Menu::whereIn('id', $ids)->delete();
        return trans('dashboard/menus.bulk_deleted');
    }
}