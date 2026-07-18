<?php

namespace App\Infrastructure\Repositories\Cameras;

use App\Domain\Cameras\Models\Camera;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Camera>
 */
class CameraRepository extends BaseRepository
{
    public function __construct(Camera $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Camera>
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
