<?php

namespace Database\Seeders;

use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Shared\Enums\TenantStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'tenants.view',
            'tenants.manage',
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',
            'branches.view',
            'branches.manage',
            'users.view',
            'users.manage',
            'roles.view',
            'roles.manage',
            'permissions.view',
            'permissions.manage',
            'analysis.view',
            'analysis.run',
            'inventory.view',
            'inventory.manage',
            'risk.view',
            'risk.manage',
            'policies.view',
            'policies.manage',
            'procedures.view',
            'procedures.manage',
            'personnel.view',
            'personnel.manage',
            'customers.view',
            'customers.manage',
            'suppliers.view',
            'suppliers.manage',
            'visitors.view',
            'visitors.manage',
            'cameras.view',
            'cameras.manage',
            'websites.view',
            'websites.manage',
            'cookies.view',
            'cookies.manage',
            'verbis.view',
            'verbis.manage',
            'applications.view',
            'applications.manage',
            'breaches.view',
            'breaches.manage',
            'audits.view',
            'audits.manage',
            'trainings.view',
            'trainings.manage',
            'templates.view',
            'templates.manage',
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Demo Danışmanlık',
            'slug' => 'demo-danismanlik',
            'status' => TenantStatus::Active,
            'plan' => 'standard',
            'settings' => ['locale' => 'tr'],
        ]);

        setPermissionsTeamId($tenant->id);

        $superAdmin = User::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'KVKK 360 Super Admin',
            'email' => 'admin@kvkk360.test',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => true,
        ]);

        $consultant = User::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Demo Danışman',
            'email' => 'danisman@kvkk360.test',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => false,
        ]);

        $tenant->users()->attach($consultant->id, ['is_owner' => true]);
        $tenant->users()->attach($superAdmin->id, ['is_owner' => false]);

        $adminRole = Role::findOrCreate('tenant_admin', 'web');
        $consultantRole = Role::findOrCreate('consultant', 'web');
        $adminRole->syncPermissions($permissions);
        $consultantRole->syncPermissions([
            'companies.view',
            'companies.create',
            'companies.update',
            'branches.view',
            'branches.manage',
            'analysis.view',
            'analysis.run',
            'inventory.view',
            'inventory.manage',
            'risk.view',
            'risk.manage',
            'policies.view',
            'policies.manage',
            'procedures.view',
            'procedures.manage',
            'personnel.view',
            'personnel.manage',
            'customers.view',
            'customers.manage',
            'suppliers.view',
            'suppliers.manage',
            'visitors.view',
            'visitors.manage',
            'cameras.view',
            'cameras.manage',
            'websites.view',
            'websites.manage',
            'cookies.view',
            'cookies.manage',
            'verbis.view',
            'verbis.manage',
            'applications.view',
            'applications.manage',
            'breaches.view',
            'breaches.manage',
            'audits.view',
            'audits.manage',
            'trainings.view',
            'trainings.manage',
            'templates.view',
            'templates.manage',
            'dashboard.view',
        ]);

        $consultant->assignRole($consultantRole);

        $this->call(ComplianceRuleSeeder::class);
        $this->call(DocumentTemplateSeeder::class);

        $company = Company::query()->create([
            'tenant_id' => $tenant->id,
            'uuid' => (string) Str::uuid(),
            'trade_name' => 'Örnek Teknoloji',
            'title' => 'Örnek Teknoloji Anonim Şirketi',
            'tax_number' => '1234567890',
            'tax_office' => 'Kadıköy',
            'mersis_number' => '0123456789012345',
            'nace_code' => '62.01',
            'email' => 'info@ornekteknoloji.test',
            'phone' => '0212 000 00 00',
            'address' => 'Caferağa Mah. Demo Sk. No:1',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'authorized_person' => 'Ayşe Yılmaz',
            'authorized_title' => 'Genel Müdür',
            'activity_summary' => 'Yazılım geliştirme ve danışmanlık hizmetleri.',
            'has_camera' => true,
            'has_website' => true,
            'has_cookies' => true,
            'employee_count' => 42,
            'status' => CompanyStatus::Active,
            'metadata' => [],
        ]);

        Branch::query()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'uuid' => (string) Str::uuid(),
            'name' => 'Merkez',
            'code' => 'HQ',
            'address' => $company->address,
            'city' => $company->city,
            'district' => $company->district,
            'phone' => $company->phone,
            'is_hq' => true,
        ]);

        unset($superAdmin);
    }
}
