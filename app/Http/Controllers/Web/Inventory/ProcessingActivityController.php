<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Application\Services\Inventory\ProcessingActivityService;
use App\Domain\Inventory\Enums\LegalBasis;
use App\Domain\Inventory\Enums\ProcessingActivityStatus;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreProcessingActivityRequest;
use App\Http\Requests\Inventory\UpdateProcessingActivityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProcessingActivityController extends Controller
{
    public function __construct(
        private readonly ProcessingActivityService $activities,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', ProcessingActivity::class);

        return view('inventory.index', [
            'company' => $company,
            'activities' => $this->activities->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [ProcessingActivity::class, $company]);

        return view('inventory.create', [
            'company' => $company,
            'branches' => $company->branches()->orderByDesc('is_hq')->orderBy('name')->get(),
            'statuses' => ProcessingActivityStatus::cases(),
            'legalBases' => LegalBasis::cases(),
        ]);
    }

    public function store(StoreProcessingActivityRequest $request, Company $company): RedirectResponse
    {
        $activity = $this->activities->create($company, $request->validated());

        return redirect()
            ->route('companies.inventory.show', [$company, $activity])
            ->with('success', 'Envanter kaydı oluşturuldu.');
    }

    public function show(Company $company, ProcessingActivity $activity): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $activity);
        abort_unless((int) $activity->company_id === (int) $company->id, 404);

        $activity->load(['branch', 'riskAssessments']);

        return view('inventory.show', compact('company', 'activity'));
    }

    public function edit(Company $company, ProcessingActivity $activity): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $activity);
        abort_unless((int) $activity->company_id === (int) $company->id, 404);

        return view('inventory.edit', [
            'company' => $company,
            'activity' => $activity,
            'branches' => $company->branches()->orderByDesc('is_hq')->orderBy('name')->get(),
            'statuses' => ProcessingActivityStatus::cases(),
            'legalBases' => LegalBasis::cases(),
        ]);
    }

    public function update(
        UpdateProcessingActivityRequest $request,
        Company $company,
        ProcessingActivity $activity,
    ): RedirectResponse {
        abort_unless((int) $activity->company_id === (int) $company->id, 404);
        $this->activities->update($activity, $request->validated());

        return redirect()
            ->route('companies.inventory.show', [$company, $activity])
            ->with('success', 'Envanter kaydı güncellendi.');
    }

    public function destroy(Company $company, ProcessingActivity $activity): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $activity);
        abort_unless((int) $activity->company_id === (int) $company->id, 404);

        $this->activities->delete($activity);

        return redirect()
            ->route('companies.inventory.index', $company)
            ->with('success', 'Envanter kaydı silindi.');
    }
}
