<?php

namespace App\Repositories\Interfaces;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Collection;

interface ContractInterface
{
    /**
     * Get all contracts with their related room and tenant.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a contract by its ID or fail.
     *
     * @param  int  $id
     * @return Contract
     */
    public function find(int $id): Contract;

    /**
     * Create a new contract record.
     *
     * @param  array  $data
     * @return Contract
     */
    public function create(array $data): Contract;

    /**
     * Update a contract by ID with given data.
     *
     * @param  int    $id
     * @param  array  $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a contract by its ID.
     *
     * @param  int  $id
     * @return int  Number of deleted records (0 or 1)
     */
    public function delete(int $id): int;
}
