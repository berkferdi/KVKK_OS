<?php

namespace App\Providers;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\Identity\RoleService;
use App\Application\Services\Identity\UserService;
use App\Application\Services\Organization\BranchService;
use App\Application\Services\Organization\CompanyService;
use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Identity\UserRepository;
use App\Infrastructure\Repositories\Organization\BranchRepository;
use App\Infrastructure\Repositories\Organization\CompanyRepository;
use App\Infrastructure\Repositories\Organization\TenantRepository;
use App\Models\User;
use App\Policies\BranchPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(AuditLogger::class);

        $this->app->singleton(TenantRepository::class);
        $this->app->singleton(CompanyRepository::class);
        $this->app->singleton(BranchRepository::class);
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(CompanyService::class);
        $this->app->singleton(BranchService::class);
        $this->app->singleton(UserService::class);
        $this->app->singleton(RoleService::class);
    }

    public function boot(): void
    {
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        Gate::before(function ($user, string $ability) {
            if ($user->is_super_admin) {
                return true;
            }

            return null;
        });
    }
}
