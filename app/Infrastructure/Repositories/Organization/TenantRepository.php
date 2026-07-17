<?php

namespace App\Infrastructure\Repositories\Organization;

use App\Domain\Organization\Models\Tenant;
use App\Infrastructure\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Tenant>
 */
class TenantRepository extends BaseRepository
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Tenant
    {
        /** @var Tenant|null $tenant */
        $tenant = $this->model->newQuery()->where('slug', $slug)->first();

        return $tenant;
    }
}
