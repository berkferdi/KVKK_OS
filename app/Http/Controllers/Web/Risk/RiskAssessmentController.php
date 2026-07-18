<?php

namespace App\Http\Controllers\Web\Risk;

use App\Application\Services\Risk\RiskAssessmentService;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Risk\Enums\RiskAssessmentStatus;
use App\Domain\Risk\Models\RiskAssessment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Risk\StoreRiskAssessmentRequest;
use App\Http\Requests\Risk\UpdateRiskAssessmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RiskAssessmentController extends Controller
{
    public function __construct(
        private readonly RiskAssessmentService $risks,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', RiskAssessment::class);

        return view('risks.index', [
            'company' => $company,
            'risks' => $this->risks->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [RiskAssessment::class, $company]);

        return view('risks.create', [
            'company' => $company,
            'activities' => ProcessingActivity::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
            'statuses' => RiskAssessmentStatus::cases(),
        ]);
    }

    public function store(StoreRiskAssessmentRequest $request, Company $company): RedirectResponse
    {
        $risk = $this->risks->create($company, $request->validated());

        return redirect()
            ->route('companies.risks.show', [$company, $risk])
            ->with('success', 'Risk kaydı oluşturuldu.');
    }

    public function show(Company $company, RiskAssessment $risk): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $risk);
        abort_unless((int) $risk->company_id === (int) $company->id, 404);

        $risk->load('processingActivity');

        return view('risks.show', compact('company', 'risk'));
    }

    public function edit(Company $company, RiskAssessment $risk): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $risk);
        abort_unless((int) $risk->company_id === (int) $company->id, 404);

        return view('risks.edit', [
            'company' => $company,
            'risk' => $risk,
            'activities' => ProcessingActivity::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
            'statuses' => RiskAssessmentStatus::cases(),
        ]);
    }

    public function update(
        UpdateRiskAssessmentRequest $request,
        Company $company,
        RiskAssessment $risk,
    ): RedirectResponse {
        abort_unless((int) $risk->company_id === (int) $company->id, 404);
        $this->risks->update($risk, $request->validated());

        return redirect()
            ->route('companies.risks.show', [$company, $risk])
            ->with('success', 'Risk kaydı güncellendi.');
    }

    public function destroy(Company $company, RiskAssessment $risk): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $risk);
        abort_unless((int) $risk->company_id === (int) $company->id, 404);

        $this->risks->delete($risk);

        return redirect()
            ->route('companies.risks.index', $company)
            ->with('success', 'Risk kaydı silindi.');
    }
}
