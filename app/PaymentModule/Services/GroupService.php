<?php

namespace App\PaymentModule\Services;

use App\PaymentModule\DTO\CreateGroupData;
use App\PaymentModule\DTO\UpdateGroupData;
use App\PaymentModule\Models\Group;
use App\PaymentModule\Repositories\GroupRepo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GroupService
{
    /**
     * GroupService constructor.
     *
     * @param GroupRepo $groupRepo
     */
    public function __construct(protected GroupRepo $groupRepo)
    {
    }

    /**
     * Get all Groups
     *
     * @param array $with
     * @param array $columns
     * @return Collection
     */
    public function getAllGroups(array $with = [], array $columns = ['*']): Collection
    {
        return $this->groupRepo->getAll($with, $columns);
    }

    /**
     * Get all Groups paginated
     *
     * @param int|null $perPage
     * @param int|null $page
     * @param array $with
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getAllGroupsPaginated(?int $perPage = null, ?int $page = null, array $with = [], array $columns = ['*']): LengthAwarePaginator
    {
        return $this->groupRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get Group by id
     *
     * @param int $groupId
     * @param array $with
     * @param array $columns
     * @return Group
     */
    public function getGroup(int $groupId, array $with = [], array $columns = ['*']): Group
    {
        return $this->groupRepo->firstOrFail($groupId, $with, $columns);
    }

    /**
     * Create new Group
     *
     * @param CreateGroupData $data
     * @return Group
     */
    public function createGroup(CreateGroupData $data): Group
    {
        return $this->groupRepo->create($data->toArray());
    }

    /**
     * Update Group by id
     *
     * @param int $groupId
     * @param UpdateGroupData $data
     * @return bool
     */
    public function updateGroup(int $groupId, UpdateGroupData $data): bool
    {
        return $this->groupRepo->update($groupId, $data->toArray());
    }

    /**
     * Delete Group by id
     *
     * @param int $groupId
     * @return bool
     */
    public function deleteGroup(int $groupId): bool
    {
        return $this->groupRepo->delete($groupId);
    }
}
