---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Services/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Services;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO\Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO\Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Repositories\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service
{
    /**
     * <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service constructor.
     *
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo
     */
    public function __construct(protected <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Repo $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo)
    {
    }

    /**
     * Get all <%= h.changeCase.pascal(h.inflection.pluralize(name)) %>
     *
     * @param array $with
     * @param array $columns
     * @return Collection
     */
    public function getAll<%= h.changeCase.pascal(h.inflection.pluralize(name)) %>(array $with = [], array $columns = ['*']): Collection
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo->getAll($with, $columns);
    }

    /**
     * Get all <%= h.changeCase.pascal(h.inflection.pluralize(name)) %> paginated
     *
     * @param int|null $perPage
     * @param int|null $page
     * @param array $with
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getAll<%= h.changeCase.pascal(h.inflection.pluralize(name)) %>Paginated(?int $perPage = null, ?int $page = null, array $with = [], array $columns = ['*']): LengthAwarePaginator
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get <%= h.changeCase.pascal(h.inflection.singularize(name)) %> by id
     *
     * @param int $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id
     * @param array $with
     * @param array $columns
     * @return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
     */
    public function get<%= h.changeCase.pascal(h.inflection.singularize(name)) %>(int $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id, array $with = [], array $columns = ['*']): <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo->firstOrFail($<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id, $with, $columns);
    }

    /**
     * Create new <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
     *
     * @param Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data $data
     * @return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
     */
    public function create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>(Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data $data): <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo->create($data->toArray());
    }

    /**
     * Update <%= h.changeCase.pascal(h.inflection.singularize(name)) %> by id
     *
     * @param int $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id
     * @param Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data $data
     * @return bool
     */
    public function update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>(int $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id, Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data $data): bool
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo->update($<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id, $data->toArray());
    }

    /**
     * Delete <%= h.changeCase.pascal(h.inflection.singularize(name)) %> by id
     *
     * @param int $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id
     * @return bool
     */
    public function delete<%= h.changeCase.pascal(h.inflection.singularize(name)) %>(int $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id): bool
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Repo->delete($<%= h.changeCase.camel(h.inflection.singularize(name)) %>Id);
    }
}
