<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function authorizeRole(array $roles)
    {
        if (! in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
