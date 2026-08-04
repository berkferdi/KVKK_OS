<?php

namespace App\Application\Services\Organization;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Organization\CompanyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RuntimeException;

class CompanyService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly AuditLogger $auditLogger,
        private readonly TenantContext $tenantContext,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Company>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->companies->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Company
    {
        /** @var Company|null $company */
        $company = $this->companies->findByUuid($uuid);

        return $company;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Company
    {
        $tenantId = $data['tenant_id']
            ?? $this->tenantContext->id()
            ?? session('tenant_id');

        if (empty($tenantId)) {
            throw new RuntimeException('Aktif tenant bulunamadı. Firma oluşturulamaz. Çıkış yapıp tekrar giriş yapın.');
        }

        $data['tenant_id'] = (int) $tenantId;

        /** @var Company $company */
        $company = $this->companies->create($data);
        $this->auditLogger->log('company.created', $company, null, $company->toArray(), $company->tenant_id);

        return $company;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Company $company, array $data): Company
    {
        $old = $company->toArray();
        /** @var Company $updated */
        $updated = $this->companies->update($company, $data);
        $this->auditLogger->log('company.updated', $updated, $old, $updated->toArray(), $updated->tenant_id);

        return $updated;
    }

    public function delete(Company $company): bool
    {
        $old = $company->toArray();
        $deleted = $this->companies->delete($company);
        if ($deleted) {
            $this->auditLogger->log('company.deleted', $company, $old, null, $company->tenant_id);
        }

        return $deleted;
    }
}
