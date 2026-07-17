<?php
namespace App\Actions\Permission\PermissionGroup\Bulk;
use App\Actions\Permission\PermissionGroup\Bulk;
use App\Actions\Permission\PermissionGroup\Bulk\Contracts\BulkActionInterface;
use InvalidArgumentException;

class BulkActionHandler
{
    private array $actions = [
        'status'       => Bulk\BulkStatusAction::class,
        'delete'       => Bulk\BulkDeleteAction::class,
        'restore'      => Bulk\BulkRestoreAction::class,
        'force_delete' => Bulk\BulkForceDeleteAction::class,
    ];

    public function handle(string $action, array $ids, array $params = []): string
    {
        throw_unless(
            isset($this->actions[$action]),
            new InvalidArgumentException(trans('dashboard/general.error_occurred'))
        );

        /** @var BulkActionInterface $actionInstance */
        $actionInstance = app($this->actions[$action]);
        return $actionInstance->handle($ids, $params);
    }
}