<?php

namespace Kayckmatias\ZFilters;

use Illuminate\Database\Eloquent\Builder;
class FilterBuilder
{
    protected $query;
    protected $filters;

    public function __construct($query, $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    protected function filtersMake(array $filters): Builder
    {
        [$simple, $relation, $complex] = $filters;

        foreach ($this->filters as $key => $value) {
            if (!$value) {
                continue;
            }

            if ($this->checkFilterAvailable($key, $simple)) {
                $this->query
                    ->whereIn($simple[$key], $this->formatSimpleValue($value));
            }

            if ($this->checkFilterAvailable($key, $relation)) {
                $relationFilter = key($relation[$key]);
                $column = $relation[$key][$relationFilter];
                $formattedValue = $this->formatSimpleValue($value);

                $this->query->whereHas($relationFilter, function ($q) use ($column, $formattedValue) {
                    $q->whereIn($column, $formattedValue);
                });
            }

            if ($this->checkFilterAvailable($key, $complex)) {
                $complex[$key]($this->query, $value);
            }
        }

        return $this->query;
    }

    private function formatSimpleValue($value){
        return is_array($value) ? $value : [$value];
    }

    private function checkFilterAvailable($key, $filters){
        return in_array($key, array_keys($filters));
    }
}