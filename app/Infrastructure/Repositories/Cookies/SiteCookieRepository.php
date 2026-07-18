<?php

namespace App\Infrastructure\Repositories\Cookies;

use App\Domain\Cookies\Models\SiteCookie;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<SiteCookie>
 */
class SiteCookieRepository extends BaseRepository
{
    public function __construct(SiteCookie $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, SiteCookie>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('website')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
