<?php

namespace DDDHelpers;

use DDDHelpers\Commands\Test;
use Illuminate\Support\ServiceProvider;

class DDDHelpersServiceProvider extends ServiceProvider {

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Test::class,
            ]);
        }
    }
}
