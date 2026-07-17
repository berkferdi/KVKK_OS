<?php

namespace App\Http\Controllers\Web\Documents;

use App\Application\Services\Documents\PolicyDocumentService;
use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\PolicyCategory;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\StorePolicyDocumentRequest;
use App\Http\Requests\Documents\UpdatePolicyDocumentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PolicyDocumentController extends Controller
{
    public function __construct(
        private readonly PolicyDocumentService $policies,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', PolicyDocument::class);

        return view('policies.index', [
            'company' => $company,
            'policies' => $this->policies->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [PolicyDocument::class, $company]);

        return view('policies.create', [
            'company' => $company,
            'categories' => PolicyCategory::cases(),
            'statuses' => DocumentStatus::cases(),
        ]);
    }

    public function store(StorePolicyDocumentRequest $request, Company $company): RedirectResponse
    {
        $policy = $this->policies->create($company, $request->validated());

        return redirect()
            ->route('companies.policies.show', [$company, $policy])
            ->with('success', 'Politika oluşturuldu.');
    }

    public function show(Company $company, PolicyDocument $policy): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $policy);
        abort_unless($policy->company_id === $company->id, 404);

        return view('policies.show', compact('company', 'policy'));
    }

    public function edit(Company $company, PolicyDocument $policy): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $policy);
        abort_unless($policy->company_id === $company->id, 404);

        return view('policies.edit', [
            'company' => $company,
            'policy' => $policy,
            'categories' => PolicyCategory::cases(),
            'statuses' => DocumentStatus::cases(),
        ]);
    }

    public function update(
        UpdatePolicyDocumentRequest $request,
        Company $company,
        PolicyDocument $policy,
    ): RedirectResponse {
        abort_unless($policy->company_id === $company->id, 404);
        $this->policies->update($policy, $request->validated());

        return redirect()
            ->route('companies.policies.show', [$company, $policy])
            ->with('success', 'Politika güncellendi.');
    }

    public function destroy(Company $company, PolicyDocument $policy): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $policy);
        abort_unless($policy->company_id === $company->id, 404);
        $this->policies->delete($policy);

        return redirect()
            ->route('companies.policies.index', $company)
            ->with('success', 'Politika silindi.');
    }
}
