<?php

namespace App\Support\Repositories;

use App\Support\Interfaces\RepoInterface;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @template T of Model
 *
 * @implements RepoInterface<T>
 */
abstract class EloquentRepo implements RepoInterface
{
    /**
     * @var T
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     *
     * @throws BindingResolutionException
     */
    public function __construct(protected Application $app)
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name, e.g. User::class
     *
     * @return class-string<T>
     */
    abstract protected function model(): string;

    /**
     * Make Model instance
     *
     * @return T
     *
     * @throws BindingResolutionException
     */
    protected function makeModel()
    {
        return $this->model = $this->app->make($this->model());
    }

    /**
     * Whether Model uses SoftDeletes
     */
    protected function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->model));
    }

    /**
     * Get query builder instance
     *
     * @param  string[]  $with
     * @param  string[]  $columns
     */
    protected function query(
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Builder {
        // @phpstan-ignore-next-line
        $query = $this
            ->model
            ->with($with)
            ->orderBy($orderBy ?? $this->getOrderBy(), $orderDirection ?? $this->getOrderDirection())
            ->select($columns);

        // Ensure model uses SoftDeletes
        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * Get query builder instance with search applied
     *
     * @param  string[]  $with
     * @param  string[]  $columns
     */
    protected function searchQuery(
        SearchQuery $search,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Builder {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->tap(fn (Builder $query) => $search->apply($query));
    }

    /**
     * Query results will be ordered by this field by default
     */
    public function getOrderBy(): string
    {
        return config('repo.order_by');
    }

    /**
     * Query results will be ordered by this direction
     */
    public function getOrderDirection(): string
    {
        return config('repo.order_direction');
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function createMany(array $data): bool
    {
        foreach ($data as $index => $item) {
            $data[$index][$this->model->getCreatedAtColumn()] = now();
            $data[$index][$this->model->getUpdatedAtColumn()] = now();
        }

        return $this->model->insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getBySearch(
        SearchQuery $search,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection {
        return $this
            ->searchQuery(
                $search,
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->get();
    }

    /**
     * {@inheritDoc}
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
    ): Collection {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->where($column, $operator, $value)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getWhereIn(
        string $column,
        array $values,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->whereIn($column, $values)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getWhereNotIn(
        string $column,
        array $values,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): Collection {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->whereNotIn($column, $values)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function paginateAll(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): LengthAwarePaginator {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->paginate($perPage, $columns, 'page', $page);
    }

    /**
     * {@inheritDoc}
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
    ): LengthAwarePaginator {
        return $this
            ->searchQuery(
                $search,
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->paginate($perPage, $columns, 'page', $page);
    }

    /**
     * {@inheritDoc}
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
    ): LengthAwarePaginator {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->where($column, $operator, $value)
            ->paginate($perPage, $columns, 'page', $page);
    }

    /**
     * {@inheritDoc}
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
    ): LengthAwarePaginator {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->whereIn($column, $values)
            ->paginate();
    }

    /**
     * {@inheritDoc}
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
    ): LengthAwarePaginator {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->whereNotIn($column, $values)
            ->paginate();
    }

    /**
     * {@inheritDoc}
     */
    public function chunkAll(
        int $count,
        callable $callback,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ): bool {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->chunk($count, $callback);
    }

    /**
     * {@inheritDoc}
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
    ): bool {
        return $this
            ->searchQuery(
                $search,
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->chunk($count, $callback);
    }

    /**
     * {@inheritDoc}
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
    ): bool {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->where($column, $operator, $value)
            ->chunk($count, $callback);
    }

    /**
     * {@inheritDoc}
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
    ): bool {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->whereIn($column, $values)
            ->chunk($count, $callback);
    }

    /**
     * {@inheritDoc}
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
    ): bool {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->whereNotIn($column, $values)
            ->chunk($count, $callback);
    }

    /**
     * {@inheritDoc}
     */
    public function first(
        int|string $id,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ) {
        return $this
            ->firstBy($this->model->getKeyName(), $id, $with, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function firstBy(
        string $column,
        int|string|float|bool|null $value,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ) {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->where($column, '=', $value)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrFail(
        int|string $id,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ) {
        return $this
            ->firstByOrFail($this->model->getKeyName(), $id, $with, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function firstByOrFail(
        string $column,
        int|string|float|bool|null $value,
        array $with = [],
        array $columns = ['*'],
        string $orderBy = null,
        string $orderDirection = null,
        bool $withTrashed = false
    ) {
        return $this
            ->query(
                $with,
                $columns,
                $orderBy,
                $orderDirection,
                $withTrashed
            )
            ->where($column, '=', $value)
            ->firstOrFail();
    }

    /**
     * {@inheritDoc}
     */
    public function update(int|string $id, array $data): bool
    {
        return (bool) $this
            ->updateWhere($this->model->getKeyName(), '=', $id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateMany(array $ids, array $data): bool
    {
        return (bool) $this
            ->updateWhereIn($this->model->getKeyName(), $ids, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateBySearch(SearchQuery $search, array $data): bool
    {
        return (bool) $this
            ->searchQuery($search)
            ->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateWhere(string $column, string $operator, int|string|float|bool|null $value, array $data): bool
    {
        return (bool) $this
            ->query()
            ->where($column, $operator, $value)
            ->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateWhereIn(string $column, array $values, array $data): bool
    {
        return (bool) $this
            ->query()
            ->whereIn($column, $values)
            ->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateWhereNotIn(string $column, array $values, array $data): bool
    {
        return (bool) $this
            ->query()
            ->whereNotIn($column, $values)
            ->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int|string $id): bool
    {
        return $this
            ->deleteWhere($this->model->getKeyName(), '=', $id);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMany(array $ids): bool
    {
        return $this
            ->deleteWhereIn($this->model->getKeyName(), $ids);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteBySearch(SearchQuery $search): bool
    {
        return $this
            ->searchQuery($search)
            ->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteWhere(string $column, string $operator, int|string|float|bool|null $value): bool
    {
        return $this
            ->query()
            ->where($column, $operator, $value)
            ->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteWhereIn(string $column, array $values): bool
    {
        return $this
            ->query()
            ->whereIn($column, $values)
            ->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteWhereNotIn(string $column, array $values): bool
    {
        return $this
            ->query()
            ->whereNotIn($column, $values)
            ->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function forceDelete(int|string $id): bool
    {
        return $this
            ->forceDeleteWhere($this->model->getKeyName(), '=', $id);
    }

    /**
     * {@inheritDoc}
     */
    public function forceDeleteMany(array $ids): bool
    {
        return $this
            ->forceDeleteWhereIn($this->model->getKeyName(), $ids);
    }

    /**
     * {@inheritDoc}
     */
    public function forceDeleteBySearch(SearchQuery $search): bool
    {
        return $this
            ->searchQuery($search)
            ->forceDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function forceDeleteWhere(string $column, string $operator, int|string|float|bool|null $value): bool
    {
        return $this
            ->query()
            ->where($column, $operator, $value)
            ->forceDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function forceDeleteWhereIn(string $column, array $values): bool
    {
        return $this
            ->query()
            ->whereIn($column, $values)
            ->forceDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function forceDeleteWhereNotIn(string $column, array $values): bool
    {
        return $this
            ->query()
            ->whereNotIn($column, $values)
            ->forceDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function restore(int|string $id): bool
    {
        return $this
            ->restoreWhere($this->model->getKeyName(), '=', $id);
    }

    /**
     * {@inheritDoc}
     */
    public function restoreMany(array $ids): bool
    {
        return $this
            ->restoreWhereIn($this->model->getKeyName(), $ids);
    }

    /**
     * {@inheritDoc}
     */
    public function restoreBySearch(SearchQuery $search): bool
    {
        /** @phpstan-ignore-next-line */
        return (bool) $this
            ->searchQuery($search)
            ->restore();
    }

    /**
     * {@inheritDoc}
     */
    public function restoreWhere(string $column, string $operator, int|string|float|bool|null $value): bool
    {
        /** @phpstan-ignore-next-line */
        return (bool) $this
            ->query()
            ->where($column, $operator, $value)
            ->restore();
    }

    /**
     * {@inheritDoc}
     */
    public function restoreWhereIn(string $column, array $values): bool
    {
        /** @phpstan-ignore-next-line */
        return (bool) $this
            ->query()
            ->whereIn($column, $values)
            ->restore();
    }

    /**
     * {@inheritDoc}
     */
    public function restoreWhereNotIn(string $column, array $values): bool
    {
        /** @phpstan-ignore-next-line */
        return (bool) $this
            ->query()
            ->whereNotIn($column, $values)
            ->restore();
    }
}
