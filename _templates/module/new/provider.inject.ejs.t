---
inject: true
to: app/Providers/AppServiceProvider.php
after: // Providers
skip_if: \$this->app->register\(\\App\\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>ServiceProvider::class\)
---
        $this->app->register(\App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>ServiceProvider::class);
