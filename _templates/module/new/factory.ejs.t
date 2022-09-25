---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Factories/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Factory.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Factory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
<% fields.forEach(function(field){ -%>
            '<%= h.changeCase.snake(field.name) -%>' => <%- field.type === 'string' ? '$this->faker->word()' : '' -%><%- field.type === 'int' ? '$this->faker->randomNumber()' : '' -%><%- field.type === 'float' ? '$this->faker->randomFloat(2, 1, 100)' : '' -%><%- field.type === 'timestamp' ? 'Carbon::parse($this->faker->dateTime())->toDateTimeString()' : '' -%><%- field.type === 'bool' ? '$this->faker->boolean()' : '' -%>,
<% }); -%>
        ];
    }

//    /**
//     * Indicate that the <%= h.inflection.titleize(h.inflection.underscore(h.inflection.singularize(name))) %> is ............
//     *
//     * @return Factory
//     */
//    public function yourState(): Factory
//    {
//        return $this->state(function (array $attributes) {
//            return [
//                //
//            ];
//        });
//    }
}
