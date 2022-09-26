<?php

namespace App\AccountModule\Services;

use App\AccountModule\DTO\CreateJarData;
use App\AccountModule\DTO\UpdateJarData;
use App\AccountModule\Models\Jar;
use App\AccountModule\Repositories\JarRepo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JarService
{
    /**
     * JarService constructor.
     *
     * @param JarRepo $jarRepo
     */
    public function __construct(protected JarRepo $jarRepo)
    {
    }

    /**
     * Get all Jars
     *
     * @param array $with
     * @param array $columns
     * @return Collection
     */
    public function getAllJars(array $with = [], array $columns = ['*']): Collection
    {
        return $this->jarRepo->getAll($with, $columns);
    }

    /**
     * Get all Jars paginated
     *
     * @param int|null $perPage
     * @param int|null $page
     * @param array $with
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getAllJarsPaginated(?int $perPage = null, ?int $page = null, array $with = [], array $columns = ['*']): LengthAwarePaginator
    {
        return $this->jarRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get Jar by id
     *
     * @param int $jarId
     * @param array $with
     * @param array $columns
     * @return Jar
     */
    public function getJar(int $jarId, array $with = [], array $columns = ['*']): Jar
    {
        return $this->jarRepo->firstOrFail($jarId, $with, $columns);
    }

    /**
     * Create new Jar
     *
     * @param CreateJarData $data
     * @return Jar
     */
    public function createJar(CreateJarData $data): Jar
    {
        return $this->jarRepo->create($data->toArray());
    }

    /**
     * Update Jar by id
     *
     * @param int $jarId
     * @param UpdateJarData $data
     * @return bool
     */
    public function updateJar(int $jarId, UpdateJarData $data): bool
    {
        return $this->jarRepo->update($jarId, $data->toArray());
    }

    /**
     * Delete Jar by id
     *
     * @param int $jarId
     * @return bool
     */
    public function deleteJar(int $jarId): bool
    {
        return $this->jarRepo->delete($jarId);
    }
}
