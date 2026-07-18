<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Inventory\Models\ProcessingActivity;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<ProcessingActivity>
 */
class ProcessingActivityRepository extends BaseRepository
{
    public function __construct(ProcessingActivity $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, ProcessingActivity>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->latest('id')
            ->paginate($perPage);
    }
}
