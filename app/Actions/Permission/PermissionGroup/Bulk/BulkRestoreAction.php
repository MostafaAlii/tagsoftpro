<?php

namespace App\Actions\Permission\PermissionGroup\Bulk;

use App\Actions\Permission\PermissionGroup\Bulk\Contracts\BulkActionInterface;
use App\Models\PermissionGroup;

class BulkRestoreAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        PermissionGroup::withTrashed()->whereIn('id', $ids)->restore();
        return trans('dashboard/permission_groups.bulk_restored_successfully');
    }
}