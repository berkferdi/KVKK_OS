<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Documents\Enums\PackageStatus;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use ZipArchive;

class DeliveryPackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_and_download_delivery_zip(): void
    {
        Storage::fake('local');
        [$user, $tenant, $company] = $this->seedContextWithGeneratedDocument();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.delivery-packages.store', $company));

        $package = DeliveryPackage::query()->where('company_id', $company->id)->first();
        $this->assertNotNull($package);
        $this->assertSame(PackageStatus::Ready, $package->status);
        $this->assertGreaterThan(0, $package->document_count);
        $this->assertNotNull($package->file_path);
        Storage::disk('local')->assertExists((string) $package->file_path);
        $this->assertTrue(collect($package->folder_snapshot ?? [])->contains(
            fn (string $entry) => str_contains($entry, '09 Kamera') && str_ends_with($entry, '.docx')
        ));
        $response->assertRedirect(route('companies.delivery-packages.show', [$company, $package]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'delivery_package.created']);

        $binary = Storage::disk('local')->get((string) $package->file_path);
        $temp = sys_get_temp_dir().'/kvkk_test_'.uniqid().'.zip';
        file_put_contents($temp, $binary);
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($temp));
        $this->assertNotFalse($zip->locateName('KVKK360/Ornek Teknoloji/15 Teslim Dosyası/MANIFEST.txt', ZipArchive::FL_NOCASE));
        $zip->close();
        unlink($temp);

        $download = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.delivery-packages.download', [$company, $package]));

        $download->assertOk();
        $this->assertStringContainsString('.zip', (string) $download->headers->get('content-disposition'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'delivery_package.downloaded']);
    }

    public function test_package_creation_requires_generated_documents(): void
    {
        Storage::fake('local');
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'packages.view', 'packages.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'packages.view', 'packages.manage']);
        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);
        $company = Company::factory()->create(['tenant_id' => $tenant->id, 'trade_name' => 'Boş Firma']);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.delivery-packages.store', $company));

        $response->assertRedirect(route('companies.delivery-packages.index', $company));
        $response->assertSessionHas('error');
        $this->assertSame(0, DeliveryPackage::query()->count());
    }

    /**
     * @return array{0: User, 1: Tenant, 2: Company}
     */
    private function seedContextWithGeneratedDocument(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'templates.view', 'templates.manage', 'packages.view', 'packages.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'templates.view', 'templates.manage', 'packages.view', 'packages.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'trade_name' => 'Ornek Teknoloji',
            'title' => 'Ornek Teknoloji A.S.',
            'address' => 'Demo Cad. No:1',
            'mersis_number' => '0123456789012345',
            'city' => 'Istanbul',
        ]);

        $template = DocumentTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'code' => 'kamera_aydinlatma',
            'title' => 'Kamera Aydinlatma',
            'category' => 'camera',
            'body' => "Firma: {{firma_unvani}}\nAdres: {{adres}}\nMERSIS: {{mersis}}",
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ]);

        $this->assertSame(1, GeneratedDocument::query()->count());

        return [$user, $tenant, $company];
    }
}
