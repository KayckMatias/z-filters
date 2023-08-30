<?php

namespace Kayckmatias\ZFilters;

interface FilterContract
{
    public function apply();
    public function getAvailableFilters();
    public function getSimpleFilters();
    public function getComplexFilters();
}