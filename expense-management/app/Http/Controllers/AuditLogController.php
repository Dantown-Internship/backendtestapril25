<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function __construct()
    {}
    
    public function index(Request $request): JsonResponse
    {
        if(!auth()->user()->isAdmin()){
            return $this->respond('You do not have the required privileges', statusCode:Response::HTTP_UNAUTHORIZED);
        }

        $auditLog =  AuditLog::where('company_id', $request->user()->company_id)
                        ->with(['user:id,name',['company:id,name']])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return $this->respond('Audit log retrieved successfully', $auditLog->toArray());

    }
}
