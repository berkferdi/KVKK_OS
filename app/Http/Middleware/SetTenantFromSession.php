<?php

namespace App\Http\Middleware;

use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantFromSession
{
    public function __construct(
        private readonly TenantContext $tenantContext,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->session()->get('tenant_id');

        // "Beni hatırla" / eski oturumlarda tenant_id session'da olmayabilir.
        if (! $tenantId && $request->user() instanceof User) {
            $tenant = $this->resolveTenantForUser($request->user());
            if ($tenant !== null) {
                $request->session()->put('tenant_id', $tenant->id);
                $tenantId = $tenant->id;
            }
        }

        if ($tenantId) {
            $tenant = Tenant::query()->find($tenantId);
            if ($tenant !== null) {
                $this->tenantContext->set($tenant);
            } else {
                $request->session()->forget('tenant_id');
                $this->tenantContext->clear();
            }
        }

        return $next($request);
    }

    private function resolveTenantForUser(User $user): ?Tenant
    {
        $tenant = $user->tenants()->orderByPivot('is_owner', 'desc')->orderBy('tenants.id')->first();

        if ($tenant === null && $user->is_super_admin) {
            $tenant = Tenant::query()->orderBy('id')->first();
        }

        return $tenant;
    }
}
