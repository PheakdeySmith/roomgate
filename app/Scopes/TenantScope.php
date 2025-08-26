<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        if ($user && $user->role === 'landlord') {
            $builder->where('landlord_id', $user->id);
        }

        // Admin users see all data (no filter)
        // Tenants will have separate controllers/views and limited access
    }
}
