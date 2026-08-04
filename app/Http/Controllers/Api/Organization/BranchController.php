<?php

namespace App\Http\Controllers\Api\Organization;

use App\Application\Services\Organization\BranchService;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchService $branches,
    ) {}

    public function index(Company $company): AnonymousResourceCollection
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Branch::class);

        return BranchResource::collection($this->branches->forCompany($company));
    }
}
