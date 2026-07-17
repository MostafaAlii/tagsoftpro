<?php
namespace App\Actions\Permission\PermissionGroup\Bulk\Contracts;
interface BulkActionInterface {
    public function handle(array $ids, array $params = []): string;
}