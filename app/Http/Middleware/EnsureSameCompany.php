<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSameCompany
{
    use ResponseHelper;

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($request->has('company_id') && $request->company_id != $user->company_id) {
            return $this->unauthorized('You cannot access data from other companies');
        }
        
        return $next($request);
    }
} 