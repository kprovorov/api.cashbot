---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Models/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Factories\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %> extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
<% fields.forEach(function(field){ -%>
        '<%= h.changeCase.snake(field.name) -%>',
<% }); -%>
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Factory
     */
    protected static function newFactory(): <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Factory
    {
        return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Factory::new();
    }
}
