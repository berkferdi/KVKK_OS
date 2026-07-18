<?php

namespace App\Http\Controllers\Web\Breaches;

use App\Application\Services\Breaches\DataBreachService;
use App\Domain\Breaches\Enums\BreachSeverity;
use App\Domain\Breaches\Enums\BreachStatus;
use App\Domain\Breaches\Enums\BreachType;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Breaches\StoreDataBreachRequest;
use App\Http\Requests\Breaches\UpdateDataBreachRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DataBreachController extends Controller
{
    public function __construct(
        private readonly DataBreachService $breaches,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', DataBreach::class);

        return view('breaches.index', [
            'company' => $company,
            'breaches' => $this->breaches->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [DataBreach::class, $company]);

        return view('breaches.create', [
            'company' => $company,
            'breachTypes' => BreachType::cases(),
            'severities' => BreachSeverity::cases(),
            'statuses' => BreachStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreDataBreachRequest $request, Company $company): RedirectResponse
    {
        $breach = $this->breaches->create($company, $request->validated());

        return redirect()
            ->route('companies.breaches.show', [$company, $breach])
            ->with('success', 'Veri ihlali kaydı oluşturuldu.');
    }

    public function show(Company $company, DataBreach $breach): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $breach);
        abort_unless($breach->company_id === $company->id, 404);
        $breach->load('branch');

        return view('breaches.show', compact('company', 'breach'));
    }

    public function edit(Company $company, DataBreach $breach): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $breach);
        abort_unless($breach->company_id === $company->id, 404);

        return view('breaches.edit', [
            'company' => $company,
            'breach' => $breach,
            'breachTypes' => BreachType::cases(),
            'severities' => BreachSeverity::cases(),
            'statuses' => BreachStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateDataBreachRequest $request,
        Company $company,
        DataBreach $breach,
    ): RedirectResponse {
        abort_unless($breach->company_id === $company->id, 404);
        $this->breaches->update($breach, $request->validated());

        return redirect()
            ->route('companies.breaches.show', [$company, $breach])
            ->with('success', 'Veri ihlali kaydı güncellendi.');
    }

    public function destroy(Company $company, DataBreach $breach): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $breach);
        abort_unless($breach->company_id === $company->id, 404);
        $this->breaches->delete($breach);

        return redirect()
            ->route('companies.breaches.index', $company)
            ->with('success', 'Veri ihlali kaydı silindi.');
    }
}
