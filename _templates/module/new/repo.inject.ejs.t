---
inject: true
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>ServiceProvider.php
after: // Repositories
skip_if: <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo::class
---
        $this->app->bind(
            \App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Repositories\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo::class,
            \App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Repositories\Eloquent<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo::class
        );
