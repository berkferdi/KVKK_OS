<?php

namespace App\Infrastructure\Repositories\Ai;

use App\Domain\Ai\Models\AiGeneration;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<AiGeneration>
 */
class AiGenerationRepository extends BaseRepository
{
    public function __construct(AiGeneration $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, AiGeneration>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with(['template', 'analysisRun'])
            ->latest('id')
            ->paginate($perPage);
    }
}
