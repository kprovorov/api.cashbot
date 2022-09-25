---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Tests/Services/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>ServiceTest.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Tests\Services;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO\Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO\Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Services\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Exceptions\ValidationException;
use Tests\TestCase;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>ServiceTest extends TestCase
{
    /**
     * @test
     * @throws BindingResolutionException
     */
    public function it_successfully_gets_all_<%= h.changeCase.snake(h.inflection.pluralize(name)) %>(): void
    {
        /** @var Collection $<%= h.changeCase.camel(h.inflection.pluralize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.pluralize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->count(3)->create();

        $service = $this->app->make(<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service::class);
        $res = $service->getAll<%= h.changeCase.pascal(h.inflection.pluralize(name)) %>();

        $this->assertCount(3, $res);
        $<%= h.changeCase.camel(h.inflection.pluralize(name)) %>->each(fn (<%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>) => $this->assertContains(
            $<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     * @throws BindingResolutionException
     */
    public function it_successfully_gets_all_<%= h.changeCase.snake(h.inflection.pluralize(name)) %>_paginated(): void
    {
        /** @var Collection $<%= h.changeCase.camel(h.inflection.pluralize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.pluralize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->count(3)->create();

        $service = $this->app->make(<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service::class);
        $res = $service->getAll<%= h.changeCase.pascal(h.inflection.pluralize(name)) %>Paginated();

        $this->assertCount(3, $res);
        $<%= h.changeCase.camel(h.inflection.pluralize(name)) %>->each(fn (<%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>) => $this->assertContains(
            $<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     * @throws BindingResolutionException
     */
    public function it_successfully_gets_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();

        $service = $this->app->make(<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service::class);
        $res = $service->get<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id);

        $this->assertEquals($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     * @throws BindingResolutionException
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function it_successfully_creates_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->make();

        $data = new Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data($<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data->toArray());

        $service = $this->app->make(<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service::class);
        $res = $service->create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($data);

        $this->assertEquals($data->toArray(), Arr::except($res->toArray(), [
            'id',
            'created_at',
            'updated_at',
        ]));
        $this->assertDatabaseHas('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', $data->toArray());
    }

    /**
     * @test
     * @throws BindingResolutionException
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function it_successfully_updates_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();

        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->make();

        $data = new Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data($<%= h.changeCase.camel(h.inflection.singularize(name)) %>Data->toArray());

        $service = $this->app->make(<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service::class);
        $res = $service->update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', $data->toArray());
    }

    /**
     * @test
     * @throws BindingResolutionException
     */
    public function it_successfully_deletes_<%= h.changeCase.snake(h.inflection.singularize(name)) %>(): void
    {
        /** @var <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %> */
        $<%= h.changeCase.camel(h.inflection.singularize(name)) %> = <%= h.changeCase.pascal(h.inflection.singularize(name)) %>::factory()->create();

        $service = $this->app->make(<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service::class);
        $res = $service->delete<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', [
            'id' => $<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id,
        ]);
    }
}
