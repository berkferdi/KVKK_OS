<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_deploy_check_passes_in_test_environment(): void
    {
        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'jwt.secret' => 'test-jwt-secret-for-deploy-check',
            'queue.default' => 'sync',
            'cache.default' => 'array',
        ]);

        $this->artisan('deploy:check')
            ->expectsOutputToContain('deploy:check başarılı')
            ->assertSuccessful();
    }

    public function test_deploy_check_fails_without_app_key(): void
    {
        config([
            'app.key' => '',
            'jwt.secret' => 'test-jwt-secret-for-deploy-check',
            'queue.default' => 'sync',
            'cache.default' => 'array',
        ]);

        $this->artisan('deploy:check')->assertFailed();
    }

    public function test_health_endpoint_is_ok(): void
    {
        $this->get('/up')->assertOk();
    }
}
