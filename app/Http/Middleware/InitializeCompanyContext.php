<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Closure;
use App\Models\Company;

class InitializeCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $companyIdentifier = $this->getCompanyIdentifier($request);

        if ($companyIdentifier) {
            $company = $this->findCompany($companyIdentifier);

            if (!$company) {
                return response()->json(['error' => 'Invalid company.'], 404);
            }

            // Bind company globally
            App::instance('currentCompany', $company);

            // Optionally, set the company in the request object
            $request->mergeIfMissing(['currentCompany' => $company]);
            
        }

        // echo App::make('currentCompany')->id;
        // die;


        return $next($request);
    }

    /**
     * Extract the company identifier from the request.
     */
    protected function getCompanyIdentifier(Request $request): ?string
    {
        // Check path: /{company}/resource
        // $fromPath = $request->segment(1);
        // if ($this->isValid($fromPath)) {
        //     return $fromPath;
        // }

        // Check headers: X-Company-ID or X-Company-Name
        $fromHeader = $request->header('X-Company-ID') ?? $request->header('X-Company-Name');
        if ($fromHeader) {
            $fromHeader = strtolower(str($fromHeader)->slug()->toString());
            // echo $fromHeader;
            // die;
            if ($this->isValid($fromHeader)) {
                return $fromHeader;
            }
        }

        return null;
    }

    /**
     * Find the company by identifier.
     */
    protected function findCompany(string $identifier): ?Company
    {
        $company = Company::where('id', $identifier)
        ->orWhere('name', 'like', "%" . str($identifier)->replace('-', ' ')->toString() . "%")
        ->first();

        return $company  ?? auth()?->user()?->company ?? null;
    }

    /**
     * Validate the identifier.
     */
    protected function isValid($value): bool
    {
        return !empty($value) && is_string($value) && strlen($value) < 100;
    }
}
