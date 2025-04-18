<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

     public function index(): JsonResponse
    {
        $data = $this->userService->getAllUser();
        
        return response()->json(['success' => true, 'message' => 'fetch data successfully', 'data' => $data]);
    }

    public function viewUser(int $id): JsonResponse
    {
        $data = $this->userService->getUserById($id);
       
       return response()->json($data, 200);
    }

    /**
     * Update the specified company.
     *
     * @param UpdateCompanyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateUserPassword(Request $request, int $id): JsonResponse
    {
        $updateRequest = $request->validate([
            "password" => "required|min:8",
        ]);
        $responseData = $this->userService->updatePassword($id, $updateRequest);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }


    public function updateUser(Request $request, int $id): JsonResponse
    {
        $updateRequest = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users",
            "role" => "required"
        ]);
        $responseData = $this->userService->updateUser($id, $updateRequest);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

    public function deleteUser(int $id): JsonResponse
    {
        $responseData = $this->userService->deleteUser($id);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

}
