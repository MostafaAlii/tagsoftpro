<?php

namespace App\Actions\Permission\PermissionGroup\Bulk;

use App\Actions\Permission\PermissionGroup\Bulk\Contracts\BulkActionInterface;
use App\Models\PermissionGroup;

class BulkForceDeleteAction implements BulkActionInterface
{
    public function handle(array $ids, array $params = []): string
    {
        PermissionGroup::withTrashed()->whereIn('id', $ids)->forceDelete();
        return trans('dashboard/permission_groups.bulk_force_deleted_successfully');
    }
}