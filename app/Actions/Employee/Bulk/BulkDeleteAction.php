<?php
namespace App\Actions\Employee\Bulk;
use App\Actions\Employee\Bulk\Contracts\BulkActionInterface;
use App\Models\Employee;
use App\Models\Concerns\UploadMedia;
class BulkDeleteAction implements BulkActionInterface {
    use UploadMedia;
    public function handle(array $ids, array $params = []): string {
        Employee::whereIn('id', $ids)->get()
            ->each(fn($employee) => $this->deleteExistingMedia(
                'employee',
                $employee,
                null,
                'media',
                true,
                'employee'
            ));
        Employee::whereIn('id', $ids)->delete();
        return trans('dashboard/employees.bulk_deleted');
    }
}