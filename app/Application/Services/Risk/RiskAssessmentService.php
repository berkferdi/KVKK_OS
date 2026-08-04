<?php

namespace App\Application\Services\Risk;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Risk\Models\RiskAssessment;
use App\Infrastructure\Repositories\Risk\RiskAssessmentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RiskAssessmentService
{
    public function __construct(
        private readonly RiskAssessmentRepository $risks,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, RiskAssessment>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->risks->paginateForCompany($company->id, $perPage);
    }

    public function findByUuid(string $uuid): ?RiskAssessment
    {
        /** @var RiskAssessment|null $risk */
        $risk = $this->risks->findByUuid($uuid);

        return $risk;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): RiskAssessment
    {
        $data = $this->withScore($data);
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';

        /** @var RiskAssessment $risk */
        $risk = $this->risks->create($data);
        $this->auditLogger->log('risk.created', $risk, null, $risk->toArray(), $company->tenant_id);

        return $risk;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(RiskAssessment $risk, array $data): RiskAssessment
    {
        $old = $risk->toArray();
        $data = $this->withScore($data);

        /** @var RiskAssessment $updated */
        $updated = $this->risks->update($risk, $data);
        $this->auditLogger->log('risk.updated', $updated, $old, $updated->toArray(), $updated->tenant_id);

        return $updated;
    }

    public function delete(RiskAssessment $risk): bool
    {
        $old = $risk->toArray();
        $deleted = $this->risks->delete($risk);
        if ($deleted) {
            $this->auditLogger->log('risk.deleted', $risk, $old, null, $risk->tenant_id);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withScore(array $data): array
    {
        $likelihood = max(1, min(5, (int) ($data['likelihood'] ?? 1)));
        $impact = max(1, min(5, (int) ($data['impact'] ?? 1)));
        $score = RiskAssessment::calculateScore($likelihood, $impact);

        $data['likelihood'] = $likelihood;
        $data['impact'] = $impact;
        $data['score'] = $score;
        $data['risk_level'] = RiskAssessment::levelFromScore($score)->value;

        return $data;
    }
}
