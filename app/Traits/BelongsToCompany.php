<?php

namespace App\Traits;


use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        // Apply the global scope for restricting reads to the current tenant
        // Keep this if you want both read restriction and automatic creation ID
        static::addGlobalScope(new CompanyScope);

        // Set company_id automatically when creating a new model
        static::creating(function (Model $model) {
            // Check if company_id is not already set and user is authenticated
            if (Auth::check() && is_null($model->company_id)) {
                // Ensure the authenticated user has a company_id
                if ($userCompanyId = Auth::user()->company_id) {
                    $model->company_id = $userCompanyId;
                } else {
                    throw new \Exception('Authenticated user does not have a company_id.');
                }
            }
        });
    }
}
