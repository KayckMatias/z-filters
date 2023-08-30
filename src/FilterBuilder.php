<?php

namespace Kayckmatias\ZFilters;

class FilterBuilder
{
    protected $query;
    protected $filters;

    public function __construct($query, $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    protected function filtersMake($simple, $complex)
    {
        foreach($this->filters as $key => $value) {
            if(!$value){
                continue;
            }

            if($this->checkFilterAvailable($key, $simple)) {
                $this->query->whereIn($simple[$key], $this->formatSimpleValue($value));
            }

            if($this->checkFilterAvailable($key, $complex)) {
                $complex[$key]($this->query, $value);
            }
        }
    }

    private function formatSimpleValue($value){
        return is_array($value) ? $value : [$value];
    }

    private function checkFilterAvailable($key, $filters){
        return in_array($key, array_keys($filters));
    }
}