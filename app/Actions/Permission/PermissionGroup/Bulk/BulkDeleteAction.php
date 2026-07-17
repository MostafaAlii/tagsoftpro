<?php

namespace App\Actions\Permission\PermissionGroup\Bulk;

use App\Actions\Permission\PermissionGroup\Bulk\Contracts\BulkActionInterface;
use App\Models\PermissionGroup;

class BulkDeleteAction implements BulkActionInterface {
    public function handle(array $ids, array $params = []): string
    {
        PermissionGroup::whereIn('id', $ids)->delete();
        return trans('dashboard/permission_groups.bulk_deleted');
    }
}