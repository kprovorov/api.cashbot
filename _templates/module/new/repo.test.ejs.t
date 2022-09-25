---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Tests/Repositories/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>RepoTest.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Tests\Repositories;

use Tests\TestCase;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>RepoTest extends TestCase
{
}
