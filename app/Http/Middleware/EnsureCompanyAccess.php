<?php
namespace App\Http\Middleware;

use Closure;
// app/Http/Middleware/EnsureCompanyAccess.php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // For expense routes
        if ($request->route('expense')) {
            $expense = $request->route('expense');
            if ($expense->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized access to this expense'
                ], 403);
            }
        }

        // For user routes
        if ($request->route('user')) {
            $targetUser = $request->route('user');
            if ($targetUser->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized access to this user'
                ], 403);
            }
        }

        // Ensure all queries are scoped to the user's company
        if ($user) {
            // This ensures all queries automatically filter by company_id
            config(['user.company_id' => $user->company_id]);
        }

        return $next($request);
    }
}
