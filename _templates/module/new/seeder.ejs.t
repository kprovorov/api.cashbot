---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Seeders/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Seeder.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Seeders;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;
use Illuminate\Database\Seeder;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();
    }
}
