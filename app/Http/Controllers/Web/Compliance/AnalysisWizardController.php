<?php

namespace App\Http\Controllers\Web\Compliance;

use App\Application\Services\Compliance\AnalysisWizardService;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnalysisWizardController extends Controller
{
    public function __construct(
        private readonly AnalysisWizardService $wizard,
    ) {}

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [AnalysisRun::class, $company]);

        $recentRuns = AnalysisRun::query()
            ->where('company_id', $company->id)
            ->latest('id')
            ->limit(5)
            ->get();

        return view('analysis.create', compact('company', 'recentRuns'));
    }

    public function store(Company $company): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('create', [AnalysisRun::class, $company]);

        $run = $this->wizard->run($company, auth()->user());

        return redirect()
            ->route('analysis.show', $run)
            ->with('success', 'KVKK analizi tamamlandı.');
    }

    public function show(AnalysisRun $analysis): View
    {
        $this->authorize('view', $analysis);
        $analysis->load(['company', 'findings', 'triggeredByUser']);

        return view('analysis.show', ['run' => $analysis]);
    }
}
