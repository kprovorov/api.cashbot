<?php

namespace App\AccountModule\Repositories;

use App\AccountModule\Models\Jar;
use App\Support\Repositories\EloquentRepo;

/**
 * @extends EloquentRepo<Jar>
 */
class EloquentJarRepo extends EloquentRepo implements JarRepo
{
    /**
     * {@inheritDoc}
     */
    protected function model(): string
    {
        return Jar::class;
    }
}
