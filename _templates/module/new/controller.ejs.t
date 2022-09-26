---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Controllers/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Controller.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Controllers;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO\Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\DTO\Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Requests\Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Requests\Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request;
use App\Http\Controllers\Controller;
use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Services\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Exceptions\ValidationException;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Controller extends Controller
{
    /**
     * <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Controller constructor.
     *
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Service
     */
    public function __construct(protected <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Service $<%= h.changeCase.camel(h.inflection.singularize(name)) %>Service)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Service->getAll<%= h.changeCase.pascal(h.inflection.pluralize(name)) %>();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request $request
     * @return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function store(Store<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request $request): <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
    {
        $data = new Create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data($request->all());

        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Service->create<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($data);
    }

    /**
     * Display the specified resource.
     *
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
     */
    public function show(<%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Service->get<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request $request
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function update(Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Request $request, <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): <%= h.changeCase.pascal(h.inflection.singularize(name)) %>
    {
        $data = new Update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Data($request->all());

        $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Service->update<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id, $data);

        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Service->get<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return bool
     */
    public function destroy(<%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): bool
    {
        return $this-><%= h.changeCase.camel(h.inflection.singularize(name)) %>Service->delete<%= h.changeCase.pascal(h.inflection.singularize(name)) %>($<%= h.changeCase.camel(h.inflection.singularize(name)) %>->id);
    }
}
