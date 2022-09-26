---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/DTO/Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data extends DataTransferObject
{
<% fields.forEach(function(field){ -%>
    public <%= field.type === 'timestamp' ? 'string' : field.type -%> $<%= h.changeCase.snake(field.name) -%>;
<% }); -%>
}
