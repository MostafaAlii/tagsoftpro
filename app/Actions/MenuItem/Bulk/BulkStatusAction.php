<?php

namespace App\Actions\MenuItem\Bulk;

use App\Actions\MenuItem\Bulk\Contracts\BulkActionInterface;
use App\Models\MenuItem;

class BulkStatusAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        MenuItem::whereIn('id', $ids)->update(['status' => $params['status']]);
        return trans('dashboard/menu_items.bulk_status_updated');
    }
}