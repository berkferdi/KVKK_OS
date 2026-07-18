<?php

namespace App\Http\Controllers\Web\Documents;

use App\Application\Services\Documents\DocumentGenerationService;
use App\Application\Services\Documents\DocumentTemplateService;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\StoreGeneratedDocumentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class GeneratedDocumentController extends Controller
{
    public function __construct(
        private readonly DocumentGenerationService $generations,
        private readonly DocumentTemplateService $templates,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', GeneratedDocument::class);

        return view('generated-documents.index', [
            'company' => $company,
            'documents' => $this->generations->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [GeneratedDocument::class, $company]);

        return view('generated-documents.create', [
            'company' => $company,
            'templates' => $this->templates->activeForSelect(),
        ]);
    }

    public function store(StoreGeneratedDocumentRequest $request, Company $company): RedirectResponse
    {
        /** @var DocumentTemplate $template */
        $template = DocumentTemplate::query()->findOrFail($request->validated('document_template_id'));

        try {
            $document = $this->generations->generate($company, $template, $request->user());
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['document_template_id' => $e->getMessage()]);
        }

        $status = $document->status;
        $failed = $status instanceof GenerationStatus && $status === GenerationStatus::Failed;

        if ($failed) {
            return redirect()
                ->route('companies.generated-documents.show', [$company, $document])
                ->with('error', 'Belge üretilemedi: eksik placeholder alanları var.');
        }

        return redirect()
            ->route('companies.generated-documents.show', [$company, $document])
            ->with('success', 'Belge üretildi.');
    }

    public function show(Company $company, GeneratedDocument $generatedDocument): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $generatedDocument);
        abort_unless($generatedDocument->company_id === $company->id, 404);
        $generatedDocument->load('template');

        return view('generated-documents.show', [
            'company' => $company,
            'document' => $generatedDocument,
        ]);
    }

    public function destroy(Company $company, GeneratedDocument $generatedDocument): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $generatedDocument);
        abort_unless($generatedDocument->company_id === $company->id, 404);
        $this->generations->delete($generatedDocument);

        return redirect()
            ->route('companies.generated-documents.index', $company)
            ->with('success', 'Üretilen belge silindi.');
    }
}
