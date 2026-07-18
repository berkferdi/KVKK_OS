<?php

namespace App\Application\Services\Cookies;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Cookies\Enums\CookieCategory;
use App\Domain\Cookies\Enums\CookieStatus;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Cookies\SiteCookieRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SiteCookieService
{
    public function __construct(
        private readonly SiteCookieRepository $cookies,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, SiteCookie>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->cookies->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): SiteCookie
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['is_third_party'] = (bool) ($data['is_third_party'] ?? false);
        $data['requires_consent'] = (bool) ($data['requires_consent'] ?? false);

        /** @var SiteCookie $cookie */
        $cookie = $this->cookies->create($data);
        $this->auditLogger->log('cookie.created', $cookie, null, [
            'name' => $cookie->name,
            'category' => $cookie->category instanceof CookieCategory ? $cookie->category->value : null,
        ], $company->tenant_id);

        return $cookie;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SiteCookie $cookie, array $data): SiteCookie
    {
        foreach (['is_third_party', 'requires_consent'] as $flag) {
            if (array_key_exists($flag, $data)) {
                $data[$flag] = (bool) $data[$flag];
            }
        }

        $old = [
            'name' => $cookie->name,
            'status' => $cookie->status instanceof CookieStatus ? $cookie->status->value : null,
        ];
        /** @var SiteCookie $updated */
        $updated = $this->cookies->update($cookie, $data);
        $this->auditLogger->log('cookie.updated', $updated, $old, [
            'name' => $updated->name,
            'status' => $updated->status instanceof CookieStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(SiteCookie $cookie): bool
    {
        $old = ['name' => $cookie->name];
        $deleted = $this->cookies->delete($cookie);
        if ($deleted) {
            $this->auditLogger->log('cookie.deleted', $cookie, $old, null, $cookie->tenant_id);
        }

        return $deleted;
    }
}
