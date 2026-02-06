<?php

namespace App\UserModule\Services;

use App\UserModule\DTO\CreateUserData;
use App\UserModule\DTO\UpdateUserData;
use App\UserModule\Models\User;
use App\UserModule\Repositories\UserRepo;
use Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Str;

class UserService
{
    /**
     * UserService constructor.
     */
    public function __construct(protected UserRepo $userRepo) {}

    /**
     * Get all Users
     */
    public function getAllUsers(
        array $with = [],
        array $columns = ["*"],
    ): Collection {
        return $this->userRepo->getAll($with, $columns);
    }

    /**
     * Get all Users paginated
     */
    public function getAllUsersPaginated(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ["*"],
    ): LengthAwarePaginator {
        return $this->userRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get User by id
     */
    public function getUser(
        int $userId,
        array $with = [],
        array $columns = ["*"],
    ): User {
        return $this->userRepo->firstOrFail($userId, $with, $columns);
    }

    /**
     * Create new User
     */
    public function createUser(CreateUserData $data): User
    {
        return $this->userRepo->create([
            ...$data->toArray(),
            "password" => Hash::make($data->password ?? Str::random()),
        ]);
    }

    /**
     * Update User by id
     */
    public function updateUser(int $userId, UpdateUserData $data): bool
    {
        return $this->userRepo->update($userId, $data->toArray());
    }

    /**
     * Delete User by id
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepo->delete($userId);
    }
}
