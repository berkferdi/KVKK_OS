<?php

namespace App\Http\Controllers\Web\Organization;

use App\Application\Services\Organization\CompanyService;
use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreCompanyRequest;
use App\Http\Requests\Organization\UpdateCompanyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Company::class);

        $companies = $this->companyService->paginate(15);

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        $this->authorize('create', Company::class);

        return view('companies.create', [
            'statuses' => CompanyStatus::cases(),
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $company = $this->companyService->create($request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Firma oluşturuldu.');
    }

    public function show(Company $company): View
    {
        $this->authorize('view', $company);

        $company->load('branches');

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        $this->authorize('update', $company);

        return view('companies.edit', [
            'company' => $company,
            'statuses' => CompanyStatus::cases(),
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $this->companyService->update($company, $request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Firma güncellendi.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        $this->companyService->delete($company);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Firma silindi.');
    }
}
