<?php

namespace App\Infrastructure\Repositories\Customers;

use App\Domain\Customers\Models\Customer;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Customer>
 */
class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Customer>
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
