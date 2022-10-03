<?php

namespace App\PaymentModule\Controllers;

use App\Http\Controllers\Controller;
use App\PaymentModule\DTO\CreateGroupData;
use App\PaymentModule\DTO\UpdateGroupData;
use App\PaymentModule\Models\Group;
use App\PaymentModule\Requests\StoreGroupRequest;
use App\PaymentModule\Requests\UpdateGroupRequest;
use App\PaymentModule\Services\GroupService;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GroupController extends Controller
{
    /**
     * GroupController constructor.
     *
     * @param  GroupService  $groupService
     */
    public function __construct(protected GroupService $groupService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->groupService->getAllGroups();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreGroupRequest  $request
     * @return Group
     *
     * @throws UnknownProperties
     */
    public function store(StoreGroupRequest $request): Group
    {
        $data = new CreateGroupData($request->all());

        return $this->groupService->createGroup($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  Group  $group
     * @return Group
     */
    public function show(Group $group): Group
    {
        return $this->groupService->getGroup($group->id, [
            'payments.jar.account.jars',
            'payments.from_transfer.payment_from.jar',
            'payments.to_transfer.payment_to.jar',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateGroupRequest  $request
     * @param  Group  $group
     * @return Group
     *
     * @throws UnknownProperties
     */
    public function update(UpdateGroupRequest $request, Group $group): Group
    {
        $data = new UpdateGroupData($request->all());

        $this->groupService->updateGroup($group->id, $data);

        return $this->groupService->getGroup($group->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Group  $group
     * @return bool
     */
    public function destroy(Group $group): bool
    {
        return $this->groupService->deleteGroup($group->id);
    }
}
