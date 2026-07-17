<?php

namespace App\Infrastructure\Repositories\Organization;

use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Company>
 */
class CompanyRepository extends BaseRepository
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }
}
