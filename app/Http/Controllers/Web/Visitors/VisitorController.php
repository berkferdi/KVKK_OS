<?php

namespace App\Http\Controllers\Web\Visitors;

use App\Application\Services\Visitors\VisitorService;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Visitors\Enums\VisitorStatus;
use App\Domain\Visitors\Models\Visitor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Visitors\StoreVisitorRequest;
use App\Http\Requests\Visitors\UpdateVisitorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VisitorController extends Controller
{
    public function __construct(
        private readonly VisitorService $visitors,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Visitor::class);

        return view('visitors.index', [
            'company' => $company,
            'visitors' => $this->visitors->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [Visitor::class, $company]);

        return view('visitors.create', [
            'company' => $company,
            'statuses' => VisitorStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreVisitorRequest $request, Company $company): RedirectResponse
    {
        $visitor = $this->visitors->create($company, $request->validated());

        return redirect()
            ->route('companies.visitors.show', [$company, $visitor])
            ->with('success', 'Ziyaretçi kaydı oluşturuldu.');
    }

    public function show(Company $company, Visitor $visitor): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $visitor);
        abort_unless($visitor->company_id === $company->id, 404);
        $visitor->load('branch');

        return view('visitors.show', compact('company', 'visitor'));
    }

    public function edit(Company $company, Visitor $visitor): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $visitor);
        abort_unless($visitor->company_id === $company->id, 404);

        return view('visitors.edit', [
            'company' => $company,
            'visitor' => $visitor,
            'statuses' => VisitorStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateVisitorRequest $request,
        Company $company,
        Visitor $visitor,
    ): RedirectResponse {
        abort_unless($visitor->company_id === $company->id, 404);
        $this->visitors->update($visitor, $request->validated());

        return redirect()
            ->route('companies.visitors.show', [$company, $visitor])
            ->with('success', 'Ziyaretçi kaydı güncellendi.');
    }

    public function destroy(Company $company, Visitor $visitor): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $visitor);
        abort_unless($visitor->company_id === $company->id, 404);
        $this->visitors->delete($visitor);

        return redirect()
            ->route('companies.visitors.index', $company)
            ->with('success', 'Ziyaretçi kaydı silindi.');
    }
}
