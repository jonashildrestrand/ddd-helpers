<?php

namespace DDDHelpers;

use Illuminate\Support\ServiceProvider;

class ContactFormServiceProvider extends ServiceProvider {

    public function boot()
    {
    }

    public function register()
    {
        dd("test");
    }
}
