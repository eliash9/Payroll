<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser() && Auth::user()->company_id) {
            // If the user is an admin, they might want to see everything? 
            // Usually in multi-tenant, even admins are scoped to their company unless they are SUPER admins.
            // Based on the user request "manage according to their company only", everyone should be scoped.
            // If there is a 'super_admin' role that sees all, we can add a check here.
            // For now, scope everyone to their company_id.
            
            $builder->where($model->getTable() . '.company_id', Auth::user()->company_id);
        }
    }
}
