<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'action' => $this->action,
            'changes' => $this->changes,
            'expense_id' => $this->expense_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'expense' => new ExpenseResource($this->whenLoaded('expense')),
        ];
    }
}
