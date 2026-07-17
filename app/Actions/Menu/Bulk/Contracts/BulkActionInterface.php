<?php

namespace App\Actions\Menu\Bulk\Contracts;

interface BulkActionInterface
{
    public function handle(array $ids, array $params = []): string;
}