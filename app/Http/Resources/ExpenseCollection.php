<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'expenses' => $this->collection,
            'pagination' => [
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total_count' => $this->total(),
            ]
        ];
    }
}

