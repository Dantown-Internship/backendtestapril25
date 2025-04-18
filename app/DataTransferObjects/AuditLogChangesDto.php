<?php

namespace App\DataTransferObjects;

readonly class AuditLogChangesDto
{
    public function __construct(
        public array $old = [],
        public ?array $new = null,
    ) {}

    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);

        return new static(
            old: $data['old'] ?? [],
            new: $data['new'] ?? null,
        );
    }

    public function toArray()
    {
        return [
            'old' => $this->old,
            'new' => $this->new,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
