<?php

namespace App\Actions\Permission\PermissionGroup\Bulk;

use App\Actions\Permission\PermissionGroup\Bulk\Contracts\BulkActionInterface;
use App\Models\PermissionGroup;

class BulkStatusAction implements BulkActionInterface {
    public function handle(array $ids, array $params = []): string {
        PermissionGroup::whereIn('id', $ids)->update(['status' => $params['status']]);
        return trans('dashboard/permission_groups.bulk_status_updated');
    }
}