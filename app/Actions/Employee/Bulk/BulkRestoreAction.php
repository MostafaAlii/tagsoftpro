<?php
namespace App\Actions\Employee\Bulk;
use App\Actions\Employee\Bulk\Contracts\BulkActionInterface;
use App\Models\Employee;
class BulkRestoreAction implements BulkActionInterface {
    public function handle(array $ids, array $params = []): string {
        Employee::withTrashed()->whereIn('id', $ids)->restore();
        return trans('dashboard/employees.bulk_restored_successfully');
    }
}
