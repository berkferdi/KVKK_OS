<?php

namespace App\Infrastructure\Repositories\Organization;

use App\Domain\Organization\Models\Branch;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Branch>
 */
class BranchRepository extends BaseRepository
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection<int, Branch>
     */
    public function forCompany(int $companyId): Collection
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->orderByDesc('is_hq')
            ->orderBy('name')
            ->get();
    }
}
