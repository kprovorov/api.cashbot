---
inject: true
to: app/Providers/AuthServiceProvider.php
after: \$policies
skip_if: \\App\\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\\Models\\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>::class => \\App\\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\\Policies\\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Policy::class
---
        \App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>::class => \App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Policies\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Policy::class,
