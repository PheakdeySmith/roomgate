<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('userRolePrefix')) {
    /**
     * Return the route prefix (e.g. 'admin', 'landlord', 'tenant') based on the authenticated user's role.
     *
     * @return string
     */
    function userRolePrefix(): string
    {
        $user = Auth::user();

        if (!$user) {
            return '#'; // Not logged in
        }

        if ($user->hasRole('admin')) {
            return 'admin';
        }

        if ($user->hasRole('landlord')) {
            return 'landlord';
        }

        if ($user->hasRole('tenant')) {
            return 'tenant';
        }

        return '#'; // Fallback for unknown roles
    }
}
