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
        parent::booted();
        static::addGlobalScope(new CompanyScope);

        self::creating(function (Model $model) {
            // Skip auto-assign if running in console (e.g., seeder, artisan)
            if (app()->runningInConsole()) {
                return;
            }

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
