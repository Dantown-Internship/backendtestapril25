<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self EMPLOYEE()
 * @method static self MANAGER()
 * @method static self ADMIN()
 */
final class RoleEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'EMPLOYEE' => 0,
            'MANAGER' => 1,
            'ADMIN' => 2,
        ];
    }

    protected static function labels(): \Closure
    {

        return function (string $name) {
            return strtolower($name);
        };
    }
}
