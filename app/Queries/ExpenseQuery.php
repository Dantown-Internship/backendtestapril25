<?php

namespace App\Queries;

use App\Models\Management\Expenses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseQuery
{


    protected Builder $query;
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->query = Expenses::query()->with('company');
        $this->filters = $filters;
    }

    public function applyFilters(): self
    {
        if (!empty($this->filters['title'])) {
            $this->query->where('title', 'like', '%' . $this->filters['title'] . '%');
        }

        if (!empty($this->filters['company'])) {
            $this->query->whereHas(
                'company',
                fn($q) =>
                $q->where('name', 'like', '%' . $this->filters['company'] . '%')
            );
        }

        return $this;
    }

    public function orderByLatest(): self
    {
        $this->query->latest();
        return $this;
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }

    public function update(string $expenseId, array $data): Expenses
    {
        $expense = Expenses::findOrFail($expenseId);
        $expense->update($data);
        return $expense->refresh();
    }

    public function delete(string $expenseId): bool
    {
        $expense = Expenses::findOrFail($expenseId);
        return $expense->delete();
    }

    

    public function getQuery(): Builder
    {
        return $this->query;
    }
}
