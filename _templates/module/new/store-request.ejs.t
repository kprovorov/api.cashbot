---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Requests/Store<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
<% fields.forEach(function(field){ -%>
            '<%= h.changeCase.snake(field.name) -%>' => 'required|<%= field.type === "string" ? "string|max:255" : "" -%><%= field.type === "int" ? "integer" : "" -%><%= field.type === "float" ? "numeric" : "" -%><%= field.type === "timestamp" ? "date" : "" -%><%= field.type === "bool" ? "boolean" : "" -%>',
<% }); -%>
        ];
    }
}
