<?php

namespace App\Http\Controllers\Web\Verbis;

use App\Application\Services\Verbis\VerbisEntryService;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Enums\VerbisEntryStatus;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Verbis\StoreVerbisEntryRequest;
use App\Http\Requests\Verbis\UpdateVerbisEntryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VerbisEntryController extends Controller
{
    public function __construct(
        private readonly VerbisEntryService $entries,
    ) {}

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [VerbisEntry::class, $company]);

        return view('verbis.entries.create', [
            'company' => $company,
            'statuses' => VerbisEntryStatus::cases(),
            'activities' => ProcessingActivity::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreVerbisEntryRequest $request, Company $company): RedirectResponse
    {
        $entry = $this->entries->create($company, $request->validated());

        return redirect()
            ->route('companies.verbis.entries.show', [$company, $entry])
            ->with('success', 'VERBİS kaydı oluşturuldu.');
    }

    public function show(Company $company, VerbisEntry $entry): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $entry);
        abort_unless((int) $entry->company_id === (int) $company->id, 404);
        $entry->load('processingActivity');

        return view('verbis.entries.show', compact('company', 'entry'));
    }

    public function edit(Company $company, VerbisEntry $entry): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $entry);
        abort_unless((int) $entry->company_id === (int) $company->id, 404);

        return view('verbis.entries.edit', [
            'company' => $company,
            'entry' => $entry,
            'statuses' => VerbisEntryStatus::cases(),
            'activities' => ProcessingActivity::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateVerbisEntryRequest $request,
        Company $company,
        VerbisEntry $entry,
    ): RedirectResponse {
        abort_unless((int) $entry->company_id === (int) $company->id, 404);
        $this->entries->update($entry, $request->validated());

        return redirect()
            ->route('companies.verbis.entries.show', [$company, $entry])
            ->with('success', 'VERBİS kaydı güncellendi.');
    }

    public function destroy(Company $company, VerbisEntry $entry): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $entry);
        abort_unless((int) $entry->company_id === (int) $company->id, 404);
        $this->entries->delete($entry);

        return redirect()
            ->route('companies.verbis.index', $company)
            ->with('success', 'VERBİS kaydı silindi.');
    }
}
