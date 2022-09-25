---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Repositories/Eloquent<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;

/**
 * @extends EloquentRepo<<%= h.changeCase.pascal(h.inflection.singularize(name)) %>>
 */
class Eloquent<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo extends EloquentRepo implements <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo
{
    /**
     * @inheritDoc
     */
    protected function model(): string
    {
        return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::class;
    }
}
