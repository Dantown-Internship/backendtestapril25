<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case Employee = 'Employee';
}

