<?php
namespace App\Actions\Employee\Bulk;
use App\Actions\Employee\Bulk\Contracts\BulkActionInterface;
use App\Models\Employee;
class BulkStatusAction implements BulkActionInterface {
    public function handle(array $ids, array $params = []): string {
        Employee::whereIn('id', $ids)->update(['status' => $params['status']]);
        return trans('dashboard/employees.bulk_status_updated');
    }
}
