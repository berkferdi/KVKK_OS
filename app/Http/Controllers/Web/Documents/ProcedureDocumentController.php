<?php

namespace App\Http\Controllers\Web\Documents;

use App\Application\Services\Documents\ProcedureDocumentService;
use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\ProcedureCategory;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\StoreProcedureDocumentRequest;
use App\Http\Requests\Documents\UpdateProcedureDocumentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProcedureDocumentController extends Controller
{
    public function __construct(
        private readonly ProcedureDocumentService $procedures,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', ProcedureDocument::class);

        return view('procedures.index', [
            'company' => $company,
            'procedures' => $this->procedures->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [ProcedureDocument::class, $company]);

        return view('procedures.create', [
            'company' => $company,
            'categories' => ProcedureCategory::cases(),
            'statuses' => DocumentStatus::cases(),
            'policies' => PolicyDocument::query()
                ->where('company_id', $company->id)
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function store(StoreProcedureDocumentRequest $request, Company $company): RedirectResponse
    {
        $procedure = $this->procedures->create($company, $request->validated());

        return redirect()
            ->route('companies.procedures.show', [$company, $procedure])
            ->with('success', 'Prosedür oluşturuldu.');
    }

    public function show(Company $company, ProcedureDocument $procedure): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $procedure);
        abort_unless((int) $procedure->company_id === (int) $company->id, 404);
        $procedure->load('policyDocument');

        return view('procedures.show', compact('company', 'procedure'));
    }

    public function edit(Company $company, ProcedureDocument $procedure): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $procedure);
        abort_unless((int) $procedure->company_id === (int) $company->id, 404);

        return view('procedures.edit', [
            'company' => $company,
            'procedure' => $procedure,
            'categories' => ProcedureCategory::cases(),
            'statuses' => DocumentStatus::cases(),
            'policies' => PolicyDocument::query()
                ->where('company_id', $company->id)
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function update(
        UpdateProcedureDocumentRequest $request,
        Company $company,
        ProcedureDocument $procedure,
    ): RedirectResponse {
        abort_unless((int) $procedure->company_id === (int) $company->id, 404);
        $this->procedures->update($procedure, $request->validated());

        return redirect()
            ->route('companies.procedures.show', [$company, $procedure])
            ->with('success', 'Prosedür güncellendi.');
    }

    public function destroy(Company $company, ProcedureDocument $procedure): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $procedure);
        abort_unless((int) $procedure->company_id === (int) $company->id, 404);
        $this->procedures->delete($procedure);

        return redirect()
            ->route('companies.procedures.index', $company)
            ->with('success', 'Prosedür silindi.');
    }
}
