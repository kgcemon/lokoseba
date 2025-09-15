<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string|in:user,provider',
        ]);

        if ($request->input('role') == 'user') {
            $result = $this->authService->userRegister($request);
            return response()->json($result, 201);
        }else if ($request->input(['role']) == 'provider') {
            $result = $this->authService->providerRegister($request);
            return response()->json($result, 201);
        }

        return response()->json([
            'error'=> true,
            'message'=> 'Role not allowed'
        ], 401);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone'    => 'required|string|max:12|exists:users,phone',
            'password' => 'required|string|min:4',
        ]);

        $result = $this->authService->login($validated);

        return response()->json($result);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'error'=> false,
            'message'=> 'user',
            'data'=> [
                'user' => $user
            ]
        ]);
    }
}
