<?php

namespace App\Http\Middleware;

use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Tenant;
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

        if ($tenantId) {
            $tenant = Tenant::query()->find($tenantId);
            if ($tenant !== null) {
                $this->tenantContext->set($tenant);
            }
        }

        return $next($request);
    }
}
