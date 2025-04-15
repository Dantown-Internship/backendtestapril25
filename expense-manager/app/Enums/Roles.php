<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';
}
