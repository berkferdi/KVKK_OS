<?php

namespace App\Application\Services\Audits;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Audits\Enums\AuditResult;
use App\Domain\Audits\Enums\AuditStatus;
use App\Domain\Audits\Enums\AuditType;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Audits\ComplianceAuditRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ComplianceAuditService
{
    public function __construct(
        private readonly ComplianceAuditRepository $audits,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, ComplianceAudit>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->audits->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): ComplianceAudit
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data = $this->withNextAuditDueDate($data);

        /** @var ComplianceAudit $audit */
        $audit = $this->audits->create($data);
        $this->auditLogger->log('compliance_audit.created', $audit, null, [
            'title' => $audit->title,
            'audit_type' => $audit->audit_type instanceof AuditType ? $audit->audit_type->value : null,
        ], $company->tenant_id);

        return $audit;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ComplianceAudit $audit, array $data): ComplianceAudit
    {
        $data = $this->withNextAuditDueDate($data, $audit);

        $old = [
            'title' => $audit->title,
            'status' => $audit->status instanceof AuditStatus ? $audit->status->value : null,
        ];
        /** @var ComplianceAudit $updated */
        $updated = $this->audits->update($audit, $data);
        $this->auditLogger->log('compliance_audit.updated', $updated, $old, [
            'title' => $updated->title,
            'status' => $updated->status instanceof AuditStatus ? $updated->status->value : null,
            'result' => $updated->result instanceof AuditResult ? $updated->result->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(ComplianceAudit $audit): bool
    {
        $old = ['title' => $audit->title];
        $deleted = $this->audits->delete($audit);
        if ($deleted) {
            $this->auditLogger->log('compliance_audit.deleted', $audit, $old, null, $audit->tenant_id);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withNextAuditDueDate(array $data, ?ComplianceAudit $existing = null): array
    {
        if (! empty($data['next_audit_due_at'])) {
            return $data;
        }

        $status = $data['status'] ?? $existing?->status;
        $statusValue = $status instanceof AuditStatus ? $status->value : (string) $status;

        if ($statusValue !== AuditStatus::Completed->value) {
            return $data;
        }

        if ($existing !== null && $existing->next_audit_due_at !== null && ! array_key_exists('status', $data) && ! array_key_exists('completed_at', $data)) {
            return $data;
        }

        $completed = $data['completed_at'] ?? $existing?->completed_at ?? now();
        $data['completed_at'] = $data['completed_at'] ?? Carbon::parse($completed)->toDateTimeString();
        $data['next_audit_due_at'] = Carbon::parse($completed)->addYear()->toDateTimeString();

        return $data;
    }
}
