<?php

namespace App\Enums;

enum AuditLogAction: string
{
    case Update = 'update';
    case Delete = 'delete';
}
