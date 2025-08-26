<?php

use App\Models\User;
use App\Services\TenantManager;

if (!function_exists('tenant')) {
    function tenant(): ?User
    {
        return app(TenantManager::class)->getTenant();
    }
}
