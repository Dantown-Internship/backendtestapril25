<?php

namespace App\Modules\Expense\Resource;

use App\Modules\Auth\Resources\UserResource;
use App\Modules\Company\Resources\CompanyResource;
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
            'amount' => $this->amount,
            'category' => $this->category,
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
