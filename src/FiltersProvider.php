<?php

namespace Kayckmatias\ZFilters;

use Illuminate\Support\ServiceProvider;
use Kayckmatias\ZFilters\Console\Commands\MakeZFilter;

class FiltersProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        $this->commands([
            MakeZFilter::class,
        ]);
    }
}
