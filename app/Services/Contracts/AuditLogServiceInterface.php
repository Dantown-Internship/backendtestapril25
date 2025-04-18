<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuditLogServiceInterface
{
    public  function log(string $action, array $oldData, array $newData = [], ?User $user = null): void;
}
