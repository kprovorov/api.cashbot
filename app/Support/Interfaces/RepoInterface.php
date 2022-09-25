<?php

namespace App\Support\Interfaces;

use App\Support\Repositories\SearchQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 */
interface RepoInterface
{
    /**
     * Create new Entity
     *
     * @param array<string, int|string|float|bool|null> $data
     * @return T
     */
    public function create(array $data);

    /**
     * Mass create new Entities
     *
     * @param array<array<string, int|string|float|bool|null>> $data
     * @return bool
     */
    public function createMany(array $data): bool;

    /**
     * Get all Entities
     *
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return Collection
     */
    public function getAll(
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection;

    /**
     * Get all Entities by applying search query
     *
     * @param SearchQuery $search
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return Collection
     */
    public function getBySearch(
        SearchQuery $search,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection;

    /**
     * Get Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return Collection
     */
    public function getWhere(
        string $column,
        string $operator,
        int|string|float|bool|null $value,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection;

    /**
     * Get Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return Collection
     */
    public function getWhereIn(
        string $column,
        array $values,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection;

    /**
     * Get Entities where $column don't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return Collection
     */
    public function getWhereNotIn(
        string $column,
        array $values,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection;

    /**
     * Get all Entities paginated
     *
     * @param int|null $perPage
     * @param int|null $page
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return LengthAwarePaginator
     */
    public function paginateAll(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): LengthAwarePaginator;

    /**
     * Get all Entities by applying search query paginated
     *
     * @param SearchQuery $search
     * @param int|null $perPage
     * @param int|null $page
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return LengthAwarePaginator
     */
    public function paginateBySearch(
        SearchQuery $search,
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): LengthAwarePaginator;

    /**
     * Get paginated Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @param int|null $perPage
     * @param int|null $page
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return LengthAwarePaginator
     */
    public function paginateWhere(
        string $column,
        string $operator,
        int|string|float|bool|null $value,
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): LengthAwarePaginator;

    /**
     * Get paginated Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param int|null $perPage
     * @param int|null $page
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return LengthAwarePaginator
     */
    public function paginateWhereIn(
        string $column,
        array $values,
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): LengthAwarePaginator;

    /**
     * Get paginated Entities where $column don't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param int|null $perPage
     * @param int|null $page
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return LengthAwarePaginator
     */
    public function paginateWhereNotIn(
        string $column,
        array $values,
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): LengthAwarePaginator;

    /**
     * Chunk over all Entities
     *
     * @param int $count
     * @param callable $callback
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return bool
     */
    public function chunkAll(
        int $count,
        callable $callback,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): bool;

    /**
     * Chunk over all Entities by applying search query
     *
     * @param SearchQuery $search
     * @param int $count
     * @param callable $callback
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return bool
     */
    public function chunkBySearch(
        SearchQuery $search,
        int $count,
        callable $callback,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): bool;

    /**
     * Chunk over Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @param int $count
     * @param callable $callback
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return bool
     */
    public function chunkWhere(
        string $column,
        string $operator,
        int|string|float|bool|null $value,
        int $count,
        callable $callback,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): bool;

    /**
     * Chunk over Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param int $count
     * @param callable $callback
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return bool
     */
    public function chunkWhereIn(
        string $column,
        array $values,
        int $count,
        callable $callback,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): bool;

    /**
     * Chunk over Entities where $column doesn't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param int $count
     * @param callable $callback
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return bool
     */
    public function chunkWhereNotIn(
        string $column,
        array $values,
        int $count,
        callable $callback,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): bool;

    /**
     * Get single Entity by id or return null
     *
     * @param int|string $id
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return T|null
     */
    public function first(
        int|string $id,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    );

    /**
     * Get single Entity by specified column or return null
     *
     * @param string $column
     * @param int|string|float|bool|null $value
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return T|null
     */
    public function firstBy(
        string $column,
        int|string|float|bool|null $value,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    );

    /**
     * Get single Entity by id or throw exception
     *
     * @param int|string $id
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return T
     */
    public function firstOrFail(
        int|string $id,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    );

    /**
     * Get single Entity by specified column or throw exception
     *
     * @param string $column
     * @param int|string|float|bool|null $value
     * @param string[] $with
     * @param string[] $columns
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool $withTrashed
     * @return T
     */
    public function firstByOrFail(
        string $column,
        int|string|float|bool|null $value,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    );

    /**
     * Update Entity by id
     *
     * @param int|string $id
     * @param array<string, int|string|float|bool|null> $data
     * @return bool
     */
    public function update(int|string $id, array $data): bool;

    /**
     * Mass update Entities by ids
     *
     * @param int[]|string[] $ids
     * @param array<string, int|string|float|bool|null> $data
     * @return bool
     */
    public function updateMany(array $ids, array $data): bool;

    /**
     * Update Entity by applying search query
     *
     * @param SearchQuery $search
     * @param array<string, int|string|float|bool|null> $data
     * @return bool
     */
    public function updateBySearch(SearchQuery $search, array $data): bool;

    /**
     * Mass update Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @param array<string, int|string|float|bool|null> $data
     * @return bool
     */
    public function updateWhere(string $column, string $operator, int|string|float|bool|null $value, array $data): bool;

    /**
     * Mass update Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param array<string, int|string|float|bool|null> $data
     * @return bool
     */
    public function updateWhereIn(string $column, array $values, array $data): bool;

    /**
     * Mass update Entities where $column don't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @param array<string, int|string|float|bool|null> $data
     * @return bool
     */
    public function updateWhereNotIn(string $column, array $values, array $data): bool;

    /**
     * Delete Entity by id
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Mass delete Entities by ids
     *
     * @param int[]|string[] $ids
     * @return bool
     */
    public function deleteMany(array $ids): bool;

    /**
     * Delete Entity by applying search query
     *
     * @param SearchQuery $search
     * @return bool
     */
    public function deleteBySearch(SearchQuery $search): bool;

    /**
     * Mass delete Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @return bool
     */
    public function deleteWhere(string $column, string $operator, int|string|float|bool|null $value): bool;

    /**
     * Mass delete Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @return bool
     */
    public function deleteWhereIn(string $column, array $values): bool;

    /**
     * Mass delete Entities where $column don't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @return bool
     */
    public function deleteWhereNotIn(string $column, array $values): bool;

    /**
     * Delete Entity by id
     *
     * @param int|string $id
     * @return bool
     */
    public function forceDelete(int|string $id): bool;

    /**
     * Mass delete Entities by ids
     *
     * @param int[]|string[] $ids
     * @return bool
     */
    public function forceDeleteMany(array $ids): bool;

    /**
     * Delete Entity by applying search query
     *
     * @param SearchQuery $search
     * @return bool
     */
    public function forceDeleteBySearch(SearchQuery $search): bool;

    /**
     * Mass delete Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @return bool
     */
    public function forceDeleteWhere(string $column, string $operator, int|string|float|bool|null $value): bool;

    /**
     * Mass delete Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @return bool
     */
    public function forceDeleteWhereIn(string $column, array $values): bool;

    /**
     * Mass delete Entities where $column don't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @return bool
     */
    public function forceDeleteWhereNotIn(string $column, array $values): bool;

    /**
     * Delete Entity by id
     *
     * @param int|string $id
     * @return bool
     */
    public function restore(int|string $id): bool;

    /**
     * Mass restore Entities by ids
     *
     * @param int[]|string[] $ids
     * @return bool
     */
    public function restoreMany(array $ids): bool;

    /**
     * Restore Entity by applying search query
     *
     * @param SearchQuery $search
     * @return bool
     */
    public function restoreBySearch(SearchQuery $search): bool;

    /**
     * Mass restore Entities that match condition
     *
     * @param string $column
     * @param string $operator
     * @param int|string|float|bool|null $value
     * @return bool
     */
    public function restoreWhere(string $column, string $operator, int|string|float|bool|null $value): bool;

    /**
     * Mass restore Entities where $column match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @return bool
     */
    public function restoreWhereIn(string $column, array $values): bool;

    /**
     * Mass restore Entities where $column don't match any of $values
     *
     * @param string $column
     * @param array<int|null>|array<string|null>|array<float|null>|array<bool|null> $values
     * @return bool
     */
    public function restoreWhereNotIn(string $column, array $values): bool;
}
