<?php

namespace App\Http\Controllers\Api\Compliance;

use App\Application\Services\Compliance\AnalysisWizardService;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnalysisRunResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct(
        private readonly AnalysisWizardService $wizard,
    ) {}

    public function store(Request $request, Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        $this->authorize('create', [AnalysisRun::class, $company]);

        $run = $this->wizard->run($company, $request->user());
        $run->load(['company', 'findings']);

        return (new AnalysisRunResource($run))
            ->response()
            ->setStatusCode(201);
    }

    public function show(AnalysisRun $analysis): AnalysisRunResource
    {
        $this->authorize('view', $analysis);
        $analysis->load(['company', 'findings']);

        return new AnalysisRunResource($analysis);
    }
}
