<?php

namespace App\Services;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class DataTableService
{
    /**
     * Generate DataTable response dynamically for any query and table.
     *
     * @param Builder|null $customQuery A custom query builder (optional).
     * @param string|null $modelClass The model class name if no custom query is provided.
     * @param Request $request The request object containing filters and pagination data.
     * @param array $columns The columns to select.
     * @param array $filterableColumns The columns allowed for filtering.
     * @param array $additionalColumns Additional columns like actions or computed fields.
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(
        ?Builder $customQuery,
        ?string $modelClass,
        Request $request,
        array $columns = [],
        array $filterableColumns = [],
        array $additionalColumns = [],
        string $filter = "created_at",
        string $orderBy = 'created_at',
        string $orderDirection = 'desc'
    ) {
        // Ensure either custom query or model class is provided
        if (!$customQuery && !$modelClass) {
            throw new \InvalidArgumentException('You must provide a custom query or a model class.');
        }
        
        // Build the query if no custom query is provided
        if (!$customQuery && $modelClass) {
            $table = (new $modelClass)->getTable();
            $validColumns = Schema::getColumnListing($table);

            if($columns){
                foreach ($columns as $column) {
                    $columnName = explode(' as ', strtolower($column))[0];
                    if (!in_array($columnName, $validColumns)) {
                        throw new \InvalidArgumentException("Column '{$column}' does not exist in the table.");
                    }
                }
            } else {
                $columns = $validColumns;
            }

            $customQuery = $modelClass::select($columns);
            // dd($customQuery);
            // $customQuery->orderBy($orderBy, $orderDirection);
        }

        // Set default values for `start` and `length` if not provided
        $defaultStart = 0;
        $defaultLength = 10;
        $defaultStartDate = "1432"; // timestamp for  "1970-01-01 00:23:52"
        $defaultEndDate = Carbon::now()->timestamp;

        $request->merge([
            'start' => $request->get('start', $defaultStart),
            'length' => $request->get('length', $defaultLength),
            'start_date' => $request->get('start_date', $defaultStartDate),
            'end_date' => $request->get('end_date', $defaultEndDate),
        ]);

        // Start DataTables with the query
        $dataTable = DataTables::eloquent($customQuery->orderBy($orderBy, $orderDirection));
        // Apply filters only on specified columns
        $dataTable->filter(function ($query) use ($request, $filterableColumns, $filter) {
            foreach ($filterableColumns as $column) {
                if ($request->filled($column)) {
                    $query->where($column, 'like', '%' . $request->input($column) . '%');
                }
            }

            if ($request->filled('search') && isset($request->search)) {
                $searchValue = $request->search;
                $query->where(function ($subQuery) use ($filterableColumns, $searchValue) {
                    foreach ($filterableColumns as $column) {
                        $subQuery->orWhere($column, 'like', '%' . $searchValue . '%');
                    }
                });
            }

            if ($request->filled('start_date') && isset($request->start_date) && $request->filled('end_date') && isset($request->end_date)) {
                $startDate = Carbon::createFromTimestamp($request->start_date)->toDateTimeString();
                $endDate = Carbon::createFromTimestamp($request->end_date)->toDateTimeString();
                $query->whereBetween($filter, [$startDate, $endDate]);
            }
        });


        // Add additional columns (e.g., actions)
        foreach ($additionalColumns as $column => $callback) {
            $dataTable->addColumn($column, $callback);
        }

        // \DB::disableQueryLog();
        // Ensure proper pagination by returning JSON response
        return $dataTable->toArray();
    }
}
