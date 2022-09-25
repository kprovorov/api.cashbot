<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class SearchQuery
{
    /**
     * @param  array  $args
     */
    public function __construct(array $args)
    {
        foreach ($args as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Apply search query
     *
     * @param  Builder  $query
     * @return Builder
     */
    abstract public function apply(Builder $query): Builder;
}
