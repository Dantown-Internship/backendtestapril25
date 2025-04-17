<?php

namespace App\Casts;

use App\DataTransferObjects\AuditLogChangesDto;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class AuditLogChangesCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): AuditLogChangesDto
    {
        return AuditLogChangesDto::fromJson($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof AuditLogChangesDto) {
            throw new RuntimeException('The value must be an instance of AuditLogChangesDto.');
        }

        return $value->toJson();
    }
}
