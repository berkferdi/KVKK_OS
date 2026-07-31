<?php

namespace App\Infrastructure\Repositories\Risk;

use App\Domain\Risk\Models\RiskAssessment;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<RiskAssessment>
 */
class RiskAssessmentRepository extends BaseRepository
{
    public function __construct(RiskAssessment $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, RiskAssessment>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('processingActivity')
            ->latest('id')
            ->paginate($perPage);
    }
}
