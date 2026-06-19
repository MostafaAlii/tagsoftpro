<?php
namespace App\Actions\Employee\Bulk;
use App\Actions\Employee\Bulk\Contracts\BulkActionInterface;
class BulkActionHandler {
    private array $actions = [
        'status'       => BulkStatusAction::class,
        'delete'       => BulkDeleteAction::class,
        'restore'      => BulkRestoreAction::class,
        'force_delete' => BulkForceDeleteAction::class,
    ];
    public function handle(string $action, array $ids, array $params = []): string
    {
        throw_unless(
            isset($this->actions[$action]),
            new \InvalidArgumentException(trans('dashboard/general.error_occurred'))
        );

        /** @var BulkActionInterface $actionInstance */
        $actionInstance = app($this->actions[$action]);
        return $actionInstance->handle($ids, $params);
    }
}
