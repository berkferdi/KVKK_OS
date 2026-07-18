<?php

namespace App\Infrastructure\Repositories\Documents;

use App\Domain\Documents\Enums\PackageStatus;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<DeliveryPackage>
 */
class DeliveryPackageRepository extends BaseRepository
{
    public function __construct(DeliveryPackage $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, DeliveryPackage>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->latest('generated_at')
            ->latest('id')
            ->paginate($perPage);
    }

    public function supersedeReadyForCompany(int $companyId): void
    {
        $this->model->newQuery()
            ->where('company_id', $companyId)
            ->where('status', PackageStatus::Ready->value)
            ->update(['status' => PackageStatus::Superseded->value]);
    }

    public function nextVersionForCompany(int $companyId): int
    {
        $max = $this->model->newQuery()
            ->where('company_id', $companyId)
            ->max('version');

        return ((int) $max) + 1;
    }
}
