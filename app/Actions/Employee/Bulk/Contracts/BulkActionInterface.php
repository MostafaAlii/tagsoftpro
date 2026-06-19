<?php
namespace App\Actions\Employee\Bulk\Contracts;
interface BulkActionInterface {
    public function handle(array $ids, array $params = []): string;
}
