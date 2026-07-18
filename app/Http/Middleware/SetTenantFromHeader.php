<?php

namespace App\Http\Middleware;

use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves tenant for JWT API requests via X-Tenant-Id (uuid) header.
 * Falls back to the user's first membership when the header is omitted.
 */
class SetTenantFromHeader
{
    public function __construct(
        private readonly TenantContext $tenantContext,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();
        if ($user === null) {
            return response()->json(['message' => 'Kimlik doğrulama gerekli.'], 401);
        }

        $header = $request->header('X-Tenant-Id') ?? $request->header('X-Tenant-UUID');
        $tenant = $this->resolveTenant($user, is_string($header) ? trim($header) : null);

        if ($tenant === null) {
            return response()->json([
                'message' => 'Kiracı seçilmedi. X-Tenant-Id (uuid) başlığı gönderin veya bir kiracıya üye olun.',
            ], 400);
        }

        if (! $user->is_super_admin && ! $user->tenants()->where('tenants.id', $tenant->id)->exists()) {
            return response()->json(['message' => 'Bu kiracıya erişim yok.'], 403);
        }

        $this->tenantContext->set($tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }

    private function resolveTenant(User $user, ?string $header): ?Tenant
    {
        if ($header !== null && $header !== '') {
            /** @var Tenant|null $byUuid */
            $byUuid = Tenant::query()->where('uuid', $header)->first();
            if ($byUuid !== null) {
                return $byUuid;
            }

            if (ctype_digit($header)) {
                /** @var Tenant|null $byId */
                $byId = Tenant::query()->find((int) $header);

                return $byId;
            }

            return null;
        }

        /** @var Tenant|null $first */
        $first = $user->tenants()->orderBy('tenants.id')->first();

        return $first;
    }
}
