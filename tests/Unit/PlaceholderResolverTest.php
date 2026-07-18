<?php

namespace Tests\Unit;

use App\Application\Services\Documents\PlaceholderResolver;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceholderResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_camera_placeholders_use_inventory_when_present(): void
    {
        $tenant = Tenant::factory()->create();
        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Demo A.Ş.',
            'mersis_number' => '0123456789012345',
        ]);

        Camera::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'location' => 'Giriş kapısı',
            'retention_days' => 20,
        ]);
        Camera::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'location' => 'Bahçe',
            'retention_days' => 15,
        ]);

        $map = app(PlaceholderResolver::class)->forCompany($company->fresh());

        $this->assertSame('2', $map->get('kamera_sayisi'));
        $this->assertStringContainsString('Giriş kapısı', (string) $map->get('kamera_alanlari'));
        $this->assertStringContainsString('Bahçe', (string) $map->get('kamera_alanlari'));
        $this->assertSame('20', $map->get('kamera_saklama_gun'));
    }

    public function test_camera_placeholders_have_defaults_without_inventory(): void
    {
        $tenant = Tenant::factory()->create();
        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Demo A.Ş.',
        ]);

        $map = app(PlaceholderResolver::class)->forCompany($company);

        $this->assertSame('belirlenen sayıda', $map->get('kamera_sayisi'));
        $this->assertNotNull($map->get('kamera_alanlari'));
        $this->assertSame('30', $map->get('kamera_saklama_gun'));
    }
}
