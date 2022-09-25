---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Tests/Controllers/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>ControllerTest.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Tests\Controllers;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>ControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_<%= h.changeCase.snake(h.inflection.pluralize(name)) %>(): void
    {
        /** @var Collection $<%= h.changeCase.camel(h.inflection.pluralize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.pluralize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->count(3)->create();

        $res = $this->get("<%= h.changeCase.param(h.inflection.pluralize(name)) %>");

        $res->assertSuccessful();
        $res->assertJson($<%= h.changeCase.camel(h.inflection.pluralize(name)) %>->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();

        $res = $this->get("<%= h.changeCase.param(h.inflection.pluralize(name)) %>/{$<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id}");

        $res->assertSuccessful();
        $res->assertJson($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->make();

        $payload = $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data->toArray();

        $res = $this->post("<%= h.changeCase.param(h.inflection.pluralize(name)) %>", $payload);

        $res->assertCreated();
        $res->assertJson($payload);
        $this->assertDatabaseHas('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_updates_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();

        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->make();

        $payload = $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data->toArray();

        $res = $this->put("<%= h.changeCase.param(h.inflection.pluralize(name)) %>/{$<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();

        $res = $this->delete("<%= h.changeCase.param(h.inflection.pluralize(name)) %>/{$<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', [
            'id' => $<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id,
        ]);
    }
}
