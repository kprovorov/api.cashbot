<?php

namespace App\UserModule\Controllers;

use App\UserModule\DTO\CreateUserData;
use App\UserModule\DTO\UpdateUserData;
use App\UserModule\Models\User;
use App\UserModule\Requests\StoreUserRequest;
use App\UserModule\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use App\UserModule\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Exceptions\ValidationException;

class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->userService->getAllUsers();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return User
     * @throws UnknownProperties
     */
    public function store(StoreUserRequest $request): User
    {
        $data = new CreateUserData($request->all());

        return $this->userService->createUser($data);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return User
     */
    public function show(User $user): User
    {
        return $this->userService->getUser($user->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return User
     * @throws UnknownProperties
     */
    public function update(UpdateUserRequest $request, User $user): User
    {
        $data = new UpdateUserData($request->all());

        $this->userService->updateUser($user->id, $data);

        return $this->userService->getUser($user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $this->userService->deleteUser($user->id);
    }
}
