<?php

namespace App\TransferModule\Services;

use App\TransferModule\DTO\CreateTransferData;
use App\TransferModule\DTO\UpdateTransferData;
use App\TransferModule\Models\Transfer;
use App\TransferModule\Repositories\TransferRepo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransferService
{
    /**
     * TransferService constructor.
     *
     * @param  TransferRepo  $transferRepo
     */
    public function __construct(protected TransferRepo $transferRepo)
    {
    }

    /**
     * Get all Transfers
     *
     * @param  array  $with
     * @param  array  $columns
     * @return Collection
     */
    public function getAllTransfers(array $with = [], array $columns = ['*']): Collection
    {
        return $this->transferRepo->getAll($with, $columns);
    }

    /**
     * Get all Transfers paginated
     *
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @param  array  $with
     * @param  array  $columns
     * @return LengthAwarePaginator
     */
    public function getAllTransfersPaginated(?int $perPage = null, ?int $page = null, array $with = [], array $columns = ['*']): LengthAwarePaginator
    {
        return $this->transferRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get Transfer by id
     *
     * @param  int  $transferId
     * @param  array  $with
     * @param  array  $columns
     * @return Transfer
     */
    public function getTransfer(int $transferId, array $with = [], array $columns = ['*']): Transfer
    {
        return $this->transferRepo->firstOrFail($transferId, $with, $columns);
    }

    /**
     * Create new Transfer
     *
     * @param  CreateTransferData  $data
     * @return Transfer
     */
    public function createTransfer(CreateTransferData $data): Transfer
    {
        return $this->transferRepo->create($data->toArray());
    }

    /**
     * Update Transfer by id
     *
     * @param  int  $transferId
     * @param  UpdateTransferData  $data
     * @return bool
     */
    public function updateTransfer(int $transferId, UpdateTransferData $data): bool
    {
        return $this->transferRepo->update($transferId, $data->toArray());
    }

    /**
     * Delete Transfer by id
     *
     * @param  int  $transferId
     * @return bool
     */
    public function deleteTransfer(int $transferId): bool
    {
        return $this->transferRepo->delete($transferId);
    }
}
