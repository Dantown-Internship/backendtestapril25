<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $targetCompanyId = $user->company_id;

        // For user-related routes (e.g., /users/{id})
        if ($request->route('id') && $request->is('api/users/*')) {
            $targetUser = User::find($request->route('id'));
            if (!$targetUser) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $targetCompanyId = $targetUser->company_id;
        }
        // For other routes (e.g., expenses), check route or body
        elseif ($request->route('company_id') || $request->input('company_id')) {
            $targetCompanyId = $request->route('company_id') ?? $request->input('company_id');
        }

        Log::info('Company authorization check', [
            'auth_user_company' => $user->company_id,
            'target_user_company' => $targetCompanyId
        ]);

        if ($user->company_id != $targetCompanyId) {
            return response()->json(['error' => 'Unauthorized access to company'], 403);
        }

        return $next($request);
    }
}