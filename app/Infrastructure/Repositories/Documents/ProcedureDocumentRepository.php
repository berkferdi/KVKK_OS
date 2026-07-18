<?php

namespace App\Infrastructure\Repositories\Documents;

use App\Domain\Documents\Models\ProcedureDocument;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<ProcedureDocument>
 */
class ProcedureDocumentRepository extends BaseRepository
{
    public function __construct(ProcedureDocument $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, ProcedureDocument>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('policyDocument')
            ->latest('id')
            ->paginate($perPage);
    }
}
