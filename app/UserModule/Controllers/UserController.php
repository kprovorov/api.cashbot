<?php

namespace App\UserModule\Controllers;

use App\Http\Controllers\Controller;
use App\UserModule\DTO\CreateUserData;
use App\UserModule\DTO\UpdateUserData;
use App\UserModule\Models\User;
use App\UserModule\Requests\StoreUserRequest;
use App\UserModule\Requests\UpdateUserRequest;
use App\UserModule\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return $this->userService->getAllUsers();
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws UnknownProperties
     */
    public function store(StoreUserRequest $request): User
    {
        $data = new CreateUserData($request->all());

        return $this->userService->createUser($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): User
    {
        return $this->userService->getUser($user->id);
    }

    /**
     * Update the specified resource in storage.
     *
     *
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
     */
    public function destroy(User $user): bool
    {
        return $this->userService->deleteUser($user->id);
    }
}
