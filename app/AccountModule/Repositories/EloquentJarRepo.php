<?php

namespace App\AccountModule\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\AccountModule\Models\Jar;

/**
 * @extends EloquentRepo<Jar>
 */
class EloquentJarRepo extends EloquentRepo implements JarRepo
{
    /**
     * @inheritDoc
     */
    protected function model(): string
    {
        return Jar::class;
    }
}
