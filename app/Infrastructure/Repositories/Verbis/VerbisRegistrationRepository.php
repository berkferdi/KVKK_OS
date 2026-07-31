<?php

namespace App\Infrastructure\Repositories\Verbis;

use App\Domain\Verbis\Models\VerbisRegistration;
use App\Infrastructure\Repositories\BaseRepository;

/**
 * @extends BaseRepository<VerbisRegistration>
 */
class VerbisRegistrationRepository extends BaseRepository
{
    public function __construct(VerbisRegistration $model)
    {
        parent::__construct($model);
    }

    public function findForCompany(int $companyId): ?VerbisRegistration
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->first();
    }
}
