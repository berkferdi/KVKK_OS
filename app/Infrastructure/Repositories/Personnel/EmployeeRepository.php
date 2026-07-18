<?php

namespace App\Infrastructure\Repositories\Personnel;

use App\Domain\Personnel\Models\Employee;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Employee>
 */
class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Employee>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('branch')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage);
    }
}
