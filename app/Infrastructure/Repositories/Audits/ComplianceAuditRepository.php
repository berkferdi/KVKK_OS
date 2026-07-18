<?php

namespace App\Infrastructure\Repositories\Audits;

use App\Domain\Audits\Models\ComplianceAudit;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<ComplianceAudit>
 */
class ComplianceAuditRepository extends BaseRepository
{
    public function __construct(ComplianceAudit $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, ComplianceAudit>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('branch')
            ->latest('planned_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
