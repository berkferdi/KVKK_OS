<?php

namespace App\Http\Controllers\Web\Documents;

use App\Application\Services\Documents\DocumentTemplateService;
use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\StoreDocumentTemplateRequest;
use App\Http\Requests\Documents\UpdateDocumentTemplateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentTemplateController extends Controller
{
    public function __construct(
        private readonly DocumentTemplateService $templates,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', DocumentTemplate::class);

        return view('document-templates.index', [
            'templates' => $this->templates->paginate(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', DocumentTemplate::class);

        return view('document-templates.create', [
            'categories' => TemplateCategory::cases(),
        ]);
    }

    public function store(StoreDocumentTemplateRequest $request): RedirectResponse
    {
        $template = $this->templates->create($request->validated());

        return redirect()
            ->route('document-templates.show', $template)
            ->with('success', 'Şablon oluşturuldu.');
    }

    public function show(DocumentTemplate $documentTemplate): View
    {
        $this->authorize('view', $documentTemplate);

        return view('document-templates.show', [
            'template' => $documentTemplate,
        ]);
    }

    public function edit(DocumentTemplate $documentTemplate): View
    {
        $this->authorize('update', $documentTemplate);

        return view('document-templates.edit', [
            'template' => $documentTemplate,
            'categories' => TemplateCategory::cases(),
        ]);
    }

    public function update(UpdateDocumentTemplateRequest $request, DocumentTemplate $documentTemplate): RedirectResponse
    {
        $this->templates->update($documentTemplate, $request->validated());

        return redirect()
            ->route('document-templates.show', $documentTemplate)
            ->with('success', 'Şablon güncellendi.');
    }

    public function destroy(DocumentTemplate $documentTemplate): RedirectResponse
    {
        $this->authorize('delete', $documentTemplate);
        $this->templates->delete($documentTemplate);

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Şablon silindi.');
    }
}
