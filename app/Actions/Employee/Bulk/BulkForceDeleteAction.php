<?php
namespace App\Actions\Employee\Bulk;
use App\Actions\Employee\Bulk\Contracts\BulkActionInterface;
use App\Models\Employee;
use App\Models\Concerns\UploadMedia;
class BulkForceDeleteAction implements BulkActionInterface {
    use UploadMedia;
    public function handle(array $ids, array $params = []): string {
        Employee::withTrashed()->whereIn('id', $ids)->get()
            ->each(fn($employee) => $this->deleteExistingMedia(
                'employee',
                $employee,
                null,
                'media',
                true,
                'employee'
            ));
        Employee::withTrashed()->whereIn('id', $ids)->forceDelete();
        return trans('dashboard/employees.bulk_force_deleted_successfully');
    }
}