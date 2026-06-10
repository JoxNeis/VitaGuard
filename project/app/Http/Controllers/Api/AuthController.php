<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    #region DEPENDENCIES

    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    #endregion

    #region AUTHENTICATION

    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
        ]);

        $result = $this->authService->login([
            ...$credentials,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Login successful',
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request
            ->user()
            ->currentAccessToken()
                ?->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    #endregion
}