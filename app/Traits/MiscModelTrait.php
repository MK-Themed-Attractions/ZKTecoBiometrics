<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use MongoDB\Laravel\Eloquent\Builder;

use function PHPUnit\Framework\isArray;

trait MiscModelTrait
{

    #region Notes
    /*
        Required protected variables in each model

            $fillable  : array
            -> default fillable of the Laravel Eloquent Model

            $relations : array
            -> list of all Laravel Eloquent Relations

        Required public functions

    */
    #endregion

    public function scopeSearchByFilters(
        Builder $query,
        array $filters,
        array $data_convert = []
    ): Builder {
        $valid_columns = $this->fillable ?? [];
        $filters = collect($filters)
            ->reject(function ($data) {
                return ! (isset($data['column']) && isset($data['values']));
            })
            ->reject(function ($data) use ($valid_columns) {
                return ! in_array($data['column'], $valid_columns);
            })
            ->toArray();
        $data_convert = collect($data_convert)
            ->reject(function ($data, $key) use ($valid_columns) {
                return ! in_array($key, $valid_columns);
            })
            ->reject(function ($data, $key) use ($filters) {
                return ! in_array($key, array_column($filters, 'column'));
            })
            ->toArray();
        foreach ($filters as $filter) {
            $isOr   = isset($filter['operation']) ? strtolower($filter['operation']) == 'or' : false;
            $column = $filter['column'];
            $values = $filter['values'];
            if (isset($data_convert[$column])) {
                $values = $this->applyDataConvert($values, $data_convert[$column]);
            }
            if ($isOr) {
                $query->orWhereIn($column, $values);
            } else {
                $query->whereIn($column, $values);
            }
        }
        return $query;
    }
    public function scopeSearchByQuery(
        Builder $query,
        string $q,
        array $columns = []
    ): Builder {
        $columns = [...$this->fillable, ...$columns];

        $query->where(function ($subQuery) use ($q, $columns) {
            foreach (explode(' ', $q) as $word) {
                foreach ($columns as $column) {
                    if (str_contains($column, '()')) {
                        // Handle relation.field
                        [$relation, $field] = explode('()', $column, 2);
                        $subQuery->orWhereHas($relation, function ($q) use ($field, $word) {
                            $q->where($field, 'LIKE', "%{$word}%");
                        });
                    } else {
                        // Direct field on model
                        $subQuery->orWhere($column, 'LIKE', "%{$word}%");
                    }
                }
            }
        });
        return $query;
    }
    public function scopewithRelations(
        Builder $query,
        array | string $relations,
        bool $use_load = false
    ): Builder {
        $relations = collect(
            array_intersect(
                is_array($relations) ? $relations : explode(',', $relations),
                $this->relations ?? []
            )
        )
            ->values()
            ->map(fn($rel) => strtolower(trim($rel)))
            ->toArray();
        if ($use_load) {
            return $this->loadMongo($query, $relations);
        }
        return $query->with($relations);
    }
    public function loadMongo(Builder $query, array $relations): Builder
    {
        $this->load($relations);
        return $query;
    }
    protected function applyDataConvert(array $values, string $type)
    {
        foreach ($values as $key => $value) {
            switch ($type) {
                case 'bool':
                    // Convert string 'true' or 'false' to boolean
                    $values[$key] = strtolower((string) $value) == 'true';
                    break;
                case 'int':
                    // Convert value to integer
                    $values[$key] = (int) $value;
                    break;
                case 'float':
                    // Convert value to float
                    $values[$key] = (float) $value;
                    break;
                case 'date':
                    // Assuming date is in 'Y-m-d' format, convert to a DateTime object
                    $values[$key] = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->toDateString();
                    break;
                    // Add more cases as needed
            }
        }

        return $values;
    }
}
