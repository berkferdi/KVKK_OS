<?php

namespace App\Application\Services\Organization;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Organization\CompanyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly AuditLogger $auditLogger,
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
        /** @var Company $company */
        $company = $this->companies->create($data);
        $this->auditLogger->log('company.created', $company, null, $company->toArray());

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
        $this->auditLogger->log('company.updated', $updated, $old, $updated->toArray());

        return $updated;
    }

    public function delete(Company $company): bool
    {
        $old = $company->toArray();
        $deleted = $this->companies->delete($company);
        if ($deleted) {
            $this->auditLogger->log('company.deleted', $company, $old, null);
        }

        return $deleted;
    }
}
