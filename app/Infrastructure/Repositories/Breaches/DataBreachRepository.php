<?php

namespace App\Infrastructure\Repositories\Breaches;

use App\Domain\Breaches\Models\DataBreach;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<DataBreach>
 */
class DataBreachRepository extends BaseRepository
{
    public function __construct(DataBreach $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, DataBreach>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('branch')
            ->latest('discovered_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
