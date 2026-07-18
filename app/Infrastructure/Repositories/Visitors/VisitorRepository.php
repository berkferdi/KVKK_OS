<?php

namespace App\Infrastructure\Repositories\Visitors;

use App\Domain\Visitors\Models\Visitor;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Visitor>
 */
class VisitorRepository extends BaseRepository
{
    public function __construct(Visitor $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Visitor>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('branch')
            ->latest('visited_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
