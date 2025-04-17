<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// app/Http/Middleware/CheckRole.php
class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action for your role');
        }

        return $next($request);
    }
}