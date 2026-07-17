<?php

namespace App\Application\Services\Organization;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Organization\BranchRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BranchService
{
    public function __construct(
        private readonly BranchRepository $branches,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return Collection<int, Branch>
     */
    public function forCompany(Company $company): Collection
    {
        return $this->branches->forCompany($company->id);
    }

    public function findByUuid(string $uuid): ?Branch
    {
        /** @var Branch|null $branch */
        $branch = $this->branches->findByUuid($uuid);

        return $branch;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Branch
    {
        return DB::transaction(function () use ($company, $data): Branch {
            if (! empty($data['is_hq'])) {
                Branch::query()
                    ->where('company_id', $company->id)
                    ->update(['is_hq' => false]);
            }

            /** @var Branch $branch */
            $branch = $this->branches->create([
                ...$data,
                'company_id' => $company->id,
                'tenant_id' => $company->tenant_id,
            ]);

            $this->auditLogger->log('branch.created', $branch, null, $branch->toArray(), $company->tenant_id);

            return $branch;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Branch $branch, array $data): Branch
    {
        return DB::transaction(function () use ($branch, $data): Branch {
            $old = $branch->toArray();

            if (! empty($data['is_hq'])) {
                Branch::query()
                    ->where('company_id', $branch->company_id)
                    ->where('id', '!=', $branch->id)
                    ->update(['is_hq' => false]);
            }

            /** @var Branch $updated */
            $updated = $this->branches->update($branch, $data);
            $this->auditLogger->log('branch.updated', $updated, $old, $updated->toArray(), $updated->tenant_id);

            return $updated;
        });
    }

    public function delete(Branch $branch): bool
    {
        $old = $branch->toArray();
        $deleted = $this->branches->delete($branch);
        if ($deleted) {
            $this->auditLogger->log('branch.deleted', $branch, $old, null, $branch->tenant_id);
        }

        return $deleted;
    }
}
