<?php

namespace App\Infrastructure\Repositories\Websites;

use App\Domain\Websites\Models\Website;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Website>
 */
class WebsiteRepository extends BaseRepository
{
    public function __construct(Website $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Website>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->paginate($perPage);
    }
}
