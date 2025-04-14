<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class InitializeCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $companyIdentifier = null;

        // Check subdomain
        $host = $request->getHost(); // e.g., {company1}.example.com
        $subdomain = explode('.', $host)[0];
        if ($this->isValidCompanyIdentifier($subdomain)) {
            $companyIdentifier = $subdomain;
        }

        // Check path: /company/{company1}/resource
        if (!$companyIdentifier) {
            $fromPath = $request->segment(1);
            if ($this->isValidCompanyIdentifier($fromPath)) {
                $companyIdentifier = $fromPath;
            }
        }

        // Check headers: X-Company-ID or X-Company-Name
        if (!$companyIdentifier) {
            $fromHeader = $request->header('X-Company-ID') ?? $request->header('X-Company-Name');
            if ($this->isValidCompanyIdentifier($fromHeader)) {
                $companyIdentifier = $fromHeader;
            }
        }

        if ($companyIdentifier) {
            $company = Company::where('id', $companyIdentifier)
                            ->orWhere('slug', $companyIdentifier)
                            ->orWhere('name', $companyIdentifier)
                            ->first();

            if ($company) {
                // Bind company globally
                app()->instance('currentCompany', $company);
                // Optionally, you can set the company in the request object
                $request->attributes->set('currentCompany', $company);

            } else {
                return response()->json(['error' => 'Invalid company.'], 404);
            }
        }

        return $next($request);
    }

    protected function isValidCompanyIdentifier($value): bool
    {
        return !empty($value) && is_string($value) && strlen($value) < 100;
    }
}
