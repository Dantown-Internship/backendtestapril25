<?php

namespace App\Http\Resources\V1\AuditLogs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ],
            'action' => $this->action,
            'changes' => $this->changes ? json_decode($this->changes) : [],
            'created_at' => $this->created_at
        ];
    }
}
