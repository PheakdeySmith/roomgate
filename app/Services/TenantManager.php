<?php
namespace App\Services;

use App\Models\User;

class TenantManager
{
    protected ?User $tenant = null;

    /**
     * Set the current tenant (landlord).
     */
    public function setTenant(User $landlord): void
    {
        $this->tenant = $landlord;
        // Bind tenant instance into Laravel container globally
        app()->instance(abstract: 'currentTenant', $landlord);
    }

    /**
     * Get the current tenant.
     */
    public function getTenant(): ?User
    {
        return $this->tenant ?? app('currentTenant');
    }
}
