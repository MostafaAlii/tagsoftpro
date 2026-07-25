<?php

namespace App\Actions\MenuItem\Bulk\Contracts;

interface BulkActionInterface
{
    public function handle(array $ids, array $params = []): string;
}