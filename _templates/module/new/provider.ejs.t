---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>ServiceProvider.php
unless_exists: true
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module;

use Illuminate\Support\ServiceProvider;

class <%= h.changeCase.pascal(h.inflection.singularize(module)) %>ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Repositories
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
