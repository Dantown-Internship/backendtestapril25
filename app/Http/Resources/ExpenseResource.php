<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'title' => $this->title,
            'category' => $this->category,
            'amount' => $this->amount,
            'owner' => UserResource::make($this->whenLoaded('owner')),
            'company' => CompanyResource::make($this->whenLoaded('company')),
        ];
    }
}
