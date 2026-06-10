<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    #region LOGIN

    public function test_user_can_login(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'jdoe01',
            'password' => 'Pass123!',
            'device_name' => 'testing',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user',
            ]);
        $user = User::where('username', $response->json('user.username'))->first();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'jdoe01',
            'password' => 'wrong-password',
            'device_name' => 'testing',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_login_requires_username(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'Pass123!',
            'device_name' => 'testing',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('username');
    }

    public function test_login_requires_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'jdoe01',
            'device_name' => 'testing',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    public function test_login_requires_device_name(): void
    {

        $response = $this->postJson('/api/auth/login', [
            'username' => 'jdoe01',
            'password' => 'Pass123!',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('device_name');
    }

    public function test_user_is_rate_limited_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'username' => 'jdoe01',
                'password' => 'wrong-password',
                'device_name' => 'testing',
            ]);
        }

        $response = $this->postJson('/api/auth/login', [
            'username' => 'jdoe01',
            'password' => 'wrong-password',
            'device_name' => 'testing',
        ]);

        $response->assertUnprocessable();
    }

    #endregion

    #region LOGOUT

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('username', 'jdoe01')->first();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/auth/logout');

        $response
            ->assertOk()
            ->assertJson(['message' => 'Logout successful']);
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->deleteJson('/api/auth/logout');
        $response->assertUnauthorized();
    }

    #endregion
}