<?php

namespace App\Http\Controllers\Web\Ai;

use App\Application\Services\Ai\AiEngineService;
use App\Application\Services\Documents\DocumentTemplateService;
use App\Domain\Ai\Enums\AiPurpose;
use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ai\StoreAiGenerationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class AiGenerationController extends Controller
{
    public function __construct(
        private readonly AiEngineService $ai,
        private readonly DocumentTemplateService $templates,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', AiGeneration::class);

        return view('ai.index', [
            'company' => $company,
            'generations' => $this->ai->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [AiGeneration::class, $company]);

        $runs = AnalysisRun::query()
            ->where('company_id', $company->id)
            ->latest('id')
            ->limit(20)
            ->get();

        return view('ai.create', [
            'company' => $company,
            'purposes' => AiPurpose::cases(),
            'templates' => $this->templates->activeForSelect(),
            'analysisRuns' => $runs,
        ]);
    }

    public function store(StoreAiGenerationRequest $request, Company $company): RedirectResponse
    {
        $data = $request->validated();
        $purpose = AiPurpose::from((string) $data['purpose']);

        try {
            $generation = match ($purpose) {
                AiPurpose::DocumentDraft => $this->ai->draftDocument(
                    $company,
                    DocumentTemplate::query()->findOrFail($data['document_template_id']),
                    $request->user(),
                ),
                AiPurpose::FindingsSummary => $this->ai->summarizeFindings(
                    $company,
                    AnalysisRun::query()->findOrFail($data['analysis_run_id']),
                    $request->user(),
                ),
            };
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['purpose' => $e->getMessage()]);
        } catch (Throwable $e) {
            return back()->with('error', 'AI üretimi başarısız: '.$e->getMessage());
        }

        return redirect()
            ->route('companies.ai.show', [$company, $generation])
            ->with('success', 'AI üretimi tamamlandı.');
    }

    public function show(Company $company, AiGeneration $ai): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $ai);
        abort_unless($ai->company_id === $company->id, 404);
        $ai->load(['template', 'analysisRun']);

        return view('ai.show', [
            'company' => $company,
            'generation' => $ai,
        ]);
    }

    public function summarizeRun(Company $company, AnalysisRun $analysis): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('create', [AiGeneration::class, $company]);
        $this->authorize('view', $analysis);
        abort_unless($analysis->company_id === $company->id, 404);

        try {
            $generation = $this->ai->summarizeFindings($company, $analysis, request()->user());
        } catch (Throwable $e) {
            return redirect()
                ->route('analysis.show', $analysis)
                ->with('error', 'AI özeti oluşturulamadı: '.$e->getMessage());
        }

        return redirect()
            ->route('companies.ai.show', [$company, $generation])
            ->with('success', 'Analiz AI özeti oluşturuldu.');
    }
}
