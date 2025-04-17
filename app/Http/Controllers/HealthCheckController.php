<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HealthCheckController extends Controller
{
    public function healthCheck(Request $request)
    {
        return response()->json(['success' => 'Hello'], 200);
    }
}
