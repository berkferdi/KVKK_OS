<?php

namespace App\Application\Services\Visitors;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Visitors\Enums\VisitorStatus;
use App\Domain\Visitors\Models\Visitor;
use App\Infrastructure\Repositories\Visitors\VisitorRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VisitorService
{
    public function __construct(
        private readonly VisitorRepository $visitors,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Visitor>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->visitors->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Visitor
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['photo_captured'] = (bool) ($data['photo_captured'] ?? false);
        $data['badge_issued'] = (bool) ($data['badge_issued'] ?? false);

        /** @var Visitor $visitor */
        $visitor = $this->visitors->create($data);
        $this->auditLogger->log('visitor.created', $visitor, null, [
            'name' => $visitor->fullName(),
            'visitor_code' => $visitor->visitor_code,
        ], $company->tenant_id);

        return $visitor;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Visitor $visitor, array $data): Visitor
    {
        if (array_key_exists('photo_captured', $data)) {
            $data['photo_captured'] = (bool) $data['photo_captured'];
        }
        if (array_key_exists('badge_issued', $data)) {
            $data['badge_issued'] = (bool) $data['badge_issued'];
        }

        $old = [
            'name' => $visitor->fullName(),
            'status' => $visitor->status instanceof VisitorStatus ? $visitor->status->value : null,
        ];
        /** @var Visitor $updated */
        $updated = $this->visitors->update($visitor, $data);
        $this->auditLogger->log('visitor.updated', $updated, $old, [
            'name' => $updated->fullName(),
            'status' => $updated->status instanceof VisitorStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(Visitor $visitor): bool
    {
        $old = ['name' => $visitor->fullName()];
        $deleted = $this->visitors->delete($visitor);
        if ($deleted) {
            $this->auditLogger->log('visitor.deleted', $visitor, $old, null, $visitor->tenant_id);
        }

        return $deleted;
    }
}
