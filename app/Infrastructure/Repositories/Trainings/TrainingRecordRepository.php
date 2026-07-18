<?php

namespace App\Infrastructure\Repositories\Trainings;

use App\Domain\Trainings\Models\TrainingRecord;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<TrainingRecord>
 */
class TrainingRecordRepository extends BaseRepository
{
    public function __construct(TrainingRecord $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, TrainingRecord>
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
