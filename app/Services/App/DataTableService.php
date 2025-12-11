<?php

namespace App\Services\App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
class DataTableService
{
    protected Builder $query;
    protected array $columns;
    protected $transform = null;
    protected $filters = null;
    protected ?array $where;
    public function __construct(
        Builder $query,
        array $columns,
        $filters,
        ?callable $transform,
        ?array $where = null
        )
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->transform = $transform;
        $this->where = $where;
    }
    public function handle(Request $request)
    {
        $query = clone $this->query;

        // Static where conditions
        if ($this->where) {
            foreach ($this->where as $field => $value) {
                $query->where($field, $value);
            }
        }

        // SEARCH (supports relations via dot notation)
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                foreach ($this->columns as $column) {
                    if (str_contains($column, '.')) {
                        // Handle relation search like: question.bank.name
                        $relations = explode('.', $column);
                        $field = array_pop($relations);
                        $relationPath = implode('.', $relations);

                        $q->orWhereHas($relationPath, function ($relQuery) use ($field, $search) {
                            $relQuery->where($field, 'like', "%{$search}%");
                        });
                    } else {
                        // Parent table search
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        // FILTERS (static + array filters)
        if ($this->filters) {
            foreach ($this->filters as $key => $value) {
                if ($value === null || $value === '') continue;

                if (is_array($value) && count($value) > 0) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        // ORDER (supports relation columns via subquery)
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $this->columns[$orderColumnIndex] ?? $this->columns[0];
        $orderDir = $request->input('order.0.dir', 'desc');

        // NEW: Handle relation-based ordering (dot notation)
        if (str_contains($orderColumn, '.')) {
            $relations = explode('.', $orderColumn);
            $field = array_pop($relations);
            $relationPath = implode('.', $relations);

            // Create a valid alias name for ordering
            $alias = str_replace('.', '_', $relationPath . '_' . $field);

            // Add the aggregate column with alias
            $query->withAggregate($relationPath, $field);

            // Correct alias name used by withAggregate
            $aggregateAlias = $relationPath . '_' . $field;

            // Use correct alias for orderBy
            $query->orderBy($aggregateAlias, $orderDir);
        } else {
            $query->orderBy($orderColumn, $orderDir);
        }

        // COUNTING
        $totalRecords = $this->query->count();
        $filteredRecords = $query->count();

        // PAGINATION
        $records = $query
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        // TRANSFORM
        $data = $this->transform ? $records->map($this->transform) : $records;

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }
}
