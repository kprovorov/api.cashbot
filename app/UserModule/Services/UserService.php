<?php

namespace App\UserModule\Services;

use App\UserModule\DTO\CreateUserData;
use App\UserModule\DTO\UpdateUserData;
use App\UserModule\Models\User;
use App\UserModule\Repositories\UserRepo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * UserService constructor.
     *
     * @param  UserRepo  $userRepo
     */
    public function __construct(protected UserRepo $userRepo)
    {
    }

    /**
     * Get all Users
     *
     * @param  array  $with
     * @param  array  $columns
     * @return Collection
     */
    public function getAllUsers(array $with = [], array $columns = ['*']): Collection
    {
        return $this->userRepo->getAll($with, $columns);
    }

    /**
     * Get all Users paginated
     *
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @param  array  $with
     * @param  array  $columns
     * @return LengthAwarePaginator
     */
    public function getAllUsersPaginated(?int $perPage = null, ?int $page = null, array $with = [], array $columns = ['*']): LengthAwarePaginator
    {
        return $this->userRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get User by id
     *
     * @param  int  $userId
     * @param  array  $with
     * @param  array  $columns
     * @return User
     */
    public function getUser(int $userId, array $with = [], array $columns = ['*']): User
    {
        return $this->userRepo->firstOrFail($userId, $with, $columns);
    }

    /**
     * Create new User
     *
     * @param  CreateUserData  $data
     * @return User
     */
    public function createUser(CreateUserData $data): User
    {
        return $this->userRepo->create($data->toArray());
    }

    /**
     * Update User by id
     *
     * @param  int  $userId
     * @param  UpdateUserData  $data
     * @return bool
     */
    public function updateUser(int $userId, UpdateUserData $data): bool
    {
        return $this->userRepo->update($userId, $data->toArray());
    }

    /**
     * Delete User by id
     *
     * @param  int  $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepo->delete($userId);
    }
}
