<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Actions\Audits\GetAuditAction;
use App\Actions\Audits\ListAuditAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    use ApiResponse;

    /**
     * Get all Audits.
     */
    public function index(Request $request, ListAuditAction $action): JsonResponse
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'expense_id' => $request->input('expense_id'),
            'action' => $request->input('action'),
        ];
        $perPage = $request->input('per_page', 15);
        $paginated = $action->handle($filters, $perPage);
        $items = AuditResource::collection($paginated->items());

        return $this->paginatedResponse('Audits fetched successfully', $items, $paginated);
    }

    /**
     * Get an Audit Details.
     */
    public function show(string $id, GetAuditAction $action)
    {
        $audit = $action->handle($id);

        return AuditResource::make($audit)->additional([
            'success' => true,
            'message' => 'Audit fetched successfully',
        ]);
    }
}
