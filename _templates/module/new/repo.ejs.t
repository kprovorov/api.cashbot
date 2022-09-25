---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Repositories/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Repositories;

use App\Support\Interfaces\RepoInterface;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;

/**
 * @extends RepoInterface<<%= h.changeCase.pascal(h.inflection.singularize(name)) %>>
 */
interface <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo extends RepoInterface
{
}
