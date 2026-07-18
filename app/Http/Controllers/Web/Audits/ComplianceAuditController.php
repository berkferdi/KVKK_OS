<?php

namespace App\Http\Controllers\Web\Audits;

use App\Application\Services\Audits\ComplianceAuditService;
use App\Domain\Audits\Enums\AuditResult;
use App\Domain\Audits\Enums\AuditStatus;
use App\Domain\Audits\Enums\AuditType;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Audits\StoreComplianceAuditRequest;
use App\Http\Requests\Audits\UpdateComplianceAuditRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplianceAuditController extends Controller
{
    public function __construct(
        private readonly ComplianceAuditService $audits,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', ComplianceAudit::class);

        return view('audits.index', [
            'company' => $company,
            'audits' => $this->audits->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [ComplianceAudit::class, $company]);

        return view('audits.create', [
            'company' => $company,
            'auditTypes' => AuditType::cases(),
            'statuses' => AuditStatus::cases(),
            'results' => AuditResult::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreComplianceAuditRequest $request, Company $company): RedirectResponse
    {
        $audit = $this->audits->create($company, $request->validated());

        return redirect()
            ->route('companies.audits.show', [$company, $audit])
            ->with('success', 'Denetim kaydı oluşturuldu.');
    }

    public function show(Company $company, ComplianceAudit $audit): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $audit);
        abort_unless($audit->company_id === $company->id, 404);
        $audit->load('branch');

        return view('audits.show', compact('company', 'audit'));
    }

    public function edit(Company $company, ComplianceAudit $audit): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $audit);
        abort_unless($audit->company_id === $company->id, 404);

        return view('audits.edit', [
            'company' => $company,
            'audit' => $audit,
            'auditTypes' => AuditType::cases(),
            'statuses' => AuditStatus::cases(),
            'results' => AuditResult::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateComplianceAuditRequest $request,
        Company $company,
        ComplianceAudit $audit,
    ): RedirectResponse {
        abort_unless($audit->company_id === $company->id, 404);
        $this->audits->update($audit, $request->validated());

        return redirect()
            ->route('companies.audits.show', [$company, $audit])
            ->with('success', 'Denetim kaydı güncellendi.');
    }

    public function destroy(Company $company, ComplianceAudit $audit): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $audit);
        abort_unless($audit->company_id === $company->id, 404);
        $this->audits->delete($audit);

        return redirect()
            ->route('companies.audits.index', $company)
            ->with('success', 'Denetim kaydı silindi.');
    }
}
