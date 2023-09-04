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
            if ($this->checkIfValueIsInvalid($value)) {
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

    /**
     * Check if a value is invalid.
     *
     * @param mixed $value The value to check.
     * @return bool Returns true if the value is invalid, false otherwise.
     */
    private function checkIfValueIsInvalid(mixed $value): bool
    {
        return (is_null($value) || $value === "" || count($value) == 0);
    }

    /**
     * Formats a simple value.
     *
     * @param mixed $value The value to format.
     * @return array The formatted value.
     */
    private function formatSimpleValue(mixed $value): array
    {
        return is_array($value) ? $value : [$value];
    }

    /**
     * Check if a given key is available in the filters array.
     *
     * @param string $key The key to check.
     * @param array $filters The filters array.
     * 
     * @return bool True if the key is available, false otherwise.
     */
    private function checkFilterAvailable(string $key, array $filters): bool
    {
        return in_array($key, array_keys($filters));
    }
}
