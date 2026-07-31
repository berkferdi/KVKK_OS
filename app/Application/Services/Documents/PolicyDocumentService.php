<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Documents\PolicyDocumentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PolicyDocumentService
{
    public function __construct(
        private readonly PolicyDocumentRepository $policies,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, PolicyDocument>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->policies->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): PolicyDocument
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';

        /** @var PolicyDocument $policy */
        $policy = $this->policies->create($data);
        $this->auditLogger->log('policy.created', $policy, null, [
            'title' => $policy->title,
        ], $company->tenant_id);

        return $policy;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PolicyDocument $policy, array $data): PolicyDocument
    {
        $old = [
            'title' => $policy->title,
            'status' => $policy->status instanceof DocumentStatus ? $policy->status->value : null,
        ];
        /** @var PolicyDocument $updated */
        $updated = $this->policies->update($policy, $data);
        $this->auditLogger->log('policy.updated', $updated, $old, [
            'title' => $updated->title,
            'status' => $updated->status instanceof DocumentStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(PolicyDocument $policy): bool
    {
        $old = ['title' => $policy->title];
        $deleted = $this->policies->delete($policy);
        if ($deleted) {
            $this->auditLogger->log('policy.deleted', $policy, $old, null, $policy->tenant_id);
        }

        return $deleted;
    }
}
