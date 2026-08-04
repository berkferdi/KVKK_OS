<?php

namespace App\Infrastructure\Repositories\Suppliers;

use App\Domain\Suppliers\Models\Supplier;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Supplier>
 */
class SupplierRepository extends BaseRepository
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Supplier>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('branch')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
