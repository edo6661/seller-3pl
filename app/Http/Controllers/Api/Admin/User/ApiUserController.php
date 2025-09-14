<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all users with filters (Admin API)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');
            $role = $request->get('role');
            $status = $request->get('status');
            $perPage = $request->get('per_page', 10);

            
            $users = $this->userService->getAllUsers($search, $role, $status);
            $stats = $this->userService->getUserStats();

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diambil',
                'data' => [
                    'users' => UserResource::collection($users->items()),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                        'from' => $users->firstItem(),
                        'to' => $users->lastItem(),
                    ],
                    'stats' => $stats,
                    'filters' => [
                        'search' => $search,
                        'role' => $role,
                        'status' => $status
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}