<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Services\DataTableService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuditController extends Controller
{
    use ControllerTrait;
    public $request;
    protected $dataTableService;

    public function __construct(Request $request, DataTableService $dataTableService)
    {
        $this->request = $request;
        $this->dataTableService = $dataTableService;
    }


    public function index(){
        $customQuery = Audit::query();
        $columns = [];
        $filter = ['action'];
        $additionalColumns = [];
        $data = $this->dataTableService->generate($customQuery, null, $this->request, $columns, $filter, $additionalColumns, 'created_at', 'created_at');
        return $this->successResponse('Records Fetched', $data, Response::HTTP_OK);
    }
}
