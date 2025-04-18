<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyMatch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $param): Response
    {
        $user = auth('sanctum')->user();
        $model = $request->route()->parameter($param); 
        if ($model && $user->company_id !== $model->company_id) {
            logger()->warning("User {$user->id} tried to access {$param} {$model->id} that does not belong to their company");
            return response()->json(['message' => str("{$param} not found")->title()->value()], 404);
        }
        return $next($request);
    }
}
