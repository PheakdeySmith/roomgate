<?php

namespace App\Repositories;

use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ContractInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContractRepository implements ContractInterface
{
    /**
     * Get all contracts with their related room and tenant.
     */
    public function all(): Collection
    {
        $user = Auth::user();

        $query = Contract::with(['room', 'tenant']);

        if ($user && $user->hasRole('landlord')) {
            $query->where('landlord_id', $user->id);
        }

        return $query->get();
    }

    /**
     * Find a contract by its ID or fail, with role check.
     */
    public function find(int $id): Contract
    {
        $contract = Contract::with(['room', 'tenant'])->findOrFail($id);

        $this->authorizeAccess($contract);

        return $contract;
    }

    /**
     * Create a new contract record.
     */
    public function create(array $data): Contract
    {
        return Contract::create($data);
    }

    /**
     * Update a contract by ID with given data.
     */
    public function update(int $id, array $data): bool
    {
        $contract = Contract::findOrFail($id);

        $this->authorizeAccess($contract);

        return $contract->update($data);
    }

    /**
     * Delete a contract by its ID.
     */
    public function delete(int $id): int
    {
        $contract = Contract::findOrFail($id);

        $this->authorizeAccess($contract);

        return $contract->delete();
    }

    /**
     * Ensure the current user is authorized to access this contract.
     */
    private function authorizeAccess(Contract $contract): void
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return; // Admins can access all
        }

        if ($user->hasRole('landlord') && $contract->landlord_id !== $user->id) {
            throw new NotFoundHttpException('Contract not found.');
        }
    }
}
