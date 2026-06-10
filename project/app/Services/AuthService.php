<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthService
{
    #region CONSTANTS
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    #endregion

    #region PUBLIC METHODS

    /**
     * @param array{
     *     username:string,
     *     password:string,
     *     ip:string,
     *     device_name:string
     * } $credentials
     *
     * @return array{
     *     token:string,
     *     user:User
     * }
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $throttleKey = $this->createThrottleKey(
            $credentials['username'],
            $credentials['ip']
        );

        $this->ensureNotRateLimited($throttleKey);

        $authenticated = Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ]);

        if (!$authenticated) {
            $this->recordFailedAttempt($throttleKey);
        }

        RateLimiter::clear($throttleKey);

        $user = $this->getAuthenticatedUser();

        return [
            'token' => $user
                ->createToken($credentials['device_name'])
                ->plainTextToken,
            'user' => $user,
        ];
    }

    #endregion

    #region AUTHENTICATION HELPERS

    /**
     */
    private function getAuthenticatedUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    #endregion

    #region RATE LIMITING HELPERS

    /**
     */
    private function createThrottleKey(
        string $username,
        string $ip
    ): string {
        return strtolower($username) . '|' . $ip;
    }

    /**
     * @throws ValidationException
     */
    private function ensureNotRateLimited(
        string $throttleKey
    ): void {
        if (
            !RateLimiter::tooManyAttempts(
                $throttleKey,
                self::MAX_ATTEMPTS
            )
        ) {
            return;
        }

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            'username' => [
                trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ],
        ]);
    }

    /**
     * @throws ValidationException
     */
    private function recordFailedAttempt(
        string $throttleKey
    ): never {
        RateLimiter::hit(
            $throttleKey,
            self::DECAY_SECONDS
        );

        throw ValidationException::withMessages([
            'username' => [
                trans('auth.failed'),
            ],
        ]);
    }

    #endregion
}