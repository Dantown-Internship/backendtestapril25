// app/Http/Kernel.php

namespace App\Http;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
protected $routeMiddleware = [
    // Other middleware...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
}
