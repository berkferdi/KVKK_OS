<?php

namespace App\Infrastructure\Repositories\Verbis;

use App\Domain\Verbis\Models\VerbisEntry;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<VerbisEntry>
 */
class VerbisEntryRepository extends BaseRepository
{
    public function __construct(VerbisEntry $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, VerbisEntry>
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
