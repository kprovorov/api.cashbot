---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/DTO/Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO;

use App\Support\DTO\DataTransferObject;

class Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data extends DataTransferObject
{
<% fields.forEach(function(field){ -%>
    public <%= field.type === 'timestamp' ? 'string' : field.type -%> $<%= h.changeCase.snake(field.name) -%>;
<% }); -%>
}
