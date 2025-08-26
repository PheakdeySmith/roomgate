<?php

namespace App\Services;

use App\Repositories\Interfaces\ContractInterface;
use App\Models\Contract;

class ContractService
{
    protected ContractInterface $contractRepo;

    /**
     * ContractService constructor.
     *
     * @param ContractInterface $contractRepo
     */
    public function __construct(ContractInterface $contractRepo)
    {
        $this->contractRepo = $contractRepo;
    }

    /**
     * Get all contracts with their related room and tenant.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->contractRepo->all();
    }

    /**
     * Create a new contract record.
     *
     * @param  array  $data
     * @return Contract
     */
    public function create(array $data): Contract
    {
        // Automatically assign the current landlord ID
        $data['landlord_id'] = tenant()->id;

        return $this->contractRepo->create($data);
    }

    /**
     * Update an existing contract by ID with new data.
     *
     * @param  int    $id
     * @param  array  $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->contractRepo->update($id, $data);
    }

    /**
     * Delete a contract by ID.
     *
     * @param  int  $id
     * @return int  Number of records deleted (0 or 1)
     */
    public function delete(int $id): int
    {
        return $this->contractRepo->delete($id);
    }
}
