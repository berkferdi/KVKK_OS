<?php

namespace App\Infrastructure\Repositories\Applications;

use App\Domain\Applications\Models\DataSubjectApplication;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<DataSubjectApplication>
 */
class DataSubjectApplicationRepository extends BaseRepository
{
    public function __construct(DataSubjectApplication $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, DataSubjectApplication>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('branch')
            ->latest('received_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
