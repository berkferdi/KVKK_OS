<?php

namespace App\Http\Controllers\Api\Organization;

use App\Application\Services\Organization\CompanyService;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreCompanyRequest;
use App\Http\Requests\Organization\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Company::class);

        return CompanyResource::collection($this->companies->paginate(15));
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = $this->companies->create($request->validated());

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Company $company): CompanyResource
    {
        $this->authorize('view', $company);
        $company->load('branches');

        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        $updated = $this->companies->update($company, $request->validated());

        return new CompanyResource($updated);
    }

    public function destroy(Company $company): Response
    {
        $this->authorize('delete', $company);
        $this->companies->delete($company);

        return response()->noContent();
    }
}
