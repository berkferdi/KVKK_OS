<?php

namespace App\Infrastructure\Repositories\Documents;

use App\Domain\Documents\Models\PolicyDocument;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<PolicyDocument>
 */
class PolicyDocumentRepository extends BaseRepository
{
    public function __construct(PolicyDocument $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, PolicyDocument>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->latest('id')
            ->paginate($perPage);
    }
}
