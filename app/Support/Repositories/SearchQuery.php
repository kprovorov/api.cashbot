<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class SearchQuery
{
    public function __construct(array $args)
    {
        foreach ($args as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Apply search query
     */
    abstract public function apply(Builder $query): Builder;
}
