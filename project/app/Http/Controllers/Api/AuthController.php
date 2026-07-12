<?php

namespace App\Http\Controllers\Api;

use App\Models\District;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;

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

        $redirectUrl = '/';
        if ($result['user']->role !== 'member') {
            $redirectUrl = '/' . $result['user']->role;
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $result['token'],
            'user' => $result['user'],
            'redirect_url' => $redirectUrl,
            // admin/home
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (method_exists($user->currentAccessToken(), 'delete')) {
            $user->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->whereNull('deleted_at'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string', 'max:255'],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
        ]);

        $this->authService->register($data);

        return response()->json([
            'message' => 'Registration successful',
            'redirect_url' => '/login',
        ], 201);
    }

    public function create(): JsonResponse
    {
        $districts = District::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'districts' => $districts,
        ], 200);
    }

    #endregion
}