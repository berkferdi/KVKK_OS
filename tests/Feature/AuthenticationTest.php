<?php

namespace Tests\Feature;

use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_users_can_authenticate_via_web(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'danisman@test.com',
            'password' => 'password',
        ]);
        $tenant->users()->attach($user->id, ['is_owner' => true]);

        $response = $this->post(route('login.store'), [
            'email' => 'danisman@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
        $this->assertEquals($tenant->id, session('tenant_id'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.login_success']);
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'danisman@test.com',
            'password' => 'password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'danisman@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.login_failed']);
    }

    public function test_inactive_users_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'pasif@test.com',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => 'pasif@test.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_jwt_login_returns_token(): void
    {
        User::factory()->create([
            'email' => 'api@test.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'api@test.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_jwt_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_jwt_me_returns_user_payload(): void
    {
        $user = User::factory()->create([
            'email' => 'api@test.com',
            'password' => 'password',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'api@test.com',
            'password' => 'password',
        ])->json();

        $this->withHeader('Authorization', 'Bearer '.$login['access_token'])
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }
}
