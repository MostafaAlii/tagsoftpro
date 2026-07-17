<?php

namespace App\Actions\Menu\Bulk;

use App\Actions\Menu\Bulk\Contracts\BulkActionInterface;
use App\Models\Menu;

class BulkStatusAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        Menu::whereIn('id', $ids)->update(['status' => $params['status']]);
        return trans('dashboard/menus.bulk_status_updated');
    }
}