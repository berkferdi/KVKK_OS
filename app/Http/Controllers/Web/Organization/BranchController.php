<?php

namespace App\Http\Controllers\Web\Organization;

use App\Application\Services\Organization\BranchService;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreBranchRequest;
use App\Http\Requests\Organization\UpdateBranchRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchService $branchService,
    ) {}

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', Branch::class);

        return view('branches.create', compact('company'));
    }

    public function store(StoreBranchRequest $request, Company $company): RedirectResponse
    {
        $this->authorize('view', $company);

        $branch = $this->branchService->create($company, $request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Şube eklendi: '.$branch->name);
    }

    public function edit(Company $company, Branch $branch): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $branch);
        abort_unless($branch->company_id === $company->id, 404);

        return view('branches.edit', compact('company', 'branch'));
    }

    public function update(UpdateBranchRequest $request, Company $company, Branch $branch): RedirectResponse
    {
        abort_unless($branch->company_id === $company->id, 404);

        $this->branchService->update($branch, $request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Şube güncellendi.');
    }

    public function destroy(Company $company, Branch $branch): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $branch);
        abort_unless($branch->company_id === $company->id, 404);

        $this->branchService->delete($branch);

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Şube silindi.');
    }
}
