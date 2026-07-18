<?php

namespace App\Http\Controllers\Web\Verbis;

use App\Application\Services\Verbis\VerbisEntryService;
use App\Application\Services\Verbis\VerbisRegistrationService;
use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Enums\VerbisRegistrationStatus;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Http\Controllers\Controller;
use App\Http\Requests\Verbis\UpdateVerbisRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VerbisController extends Controller
{
    public function __construct(
        private readonly VerbisRegistrationService $registrations,
        private readonly VerbisEntryService $entries,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', VerbisEntry::class);

        $registration = $this->registrations->getOrCreateForCompany($company);

        return view('verbis.index', [
            'company' => $company,
            'registration' => $registration,
            'entries' => $this->entries->paginateForCompany($company),
        ]);
    }

    public function editRegistration(Company $company): View
    {
        $this->authorize('view', $company);
        $registration = $this->registrations->getOrCreateForCompany($company);
        $this->authorize('updateRegistration', $registration);

        return view('verbis.registration-edit', [
            'company' => $company,
            'registration' => $registration,
            'statuses' => VerbisRegistrationStatus::cases(),
        ]);
    }

    public function updateRegistration(
        UpdateVerbisRegistrationRequest $request,
        Company $company,
        VerbisRegistration $registration,
    ): RedirectResponse {
        abort_unless($registration->company_id === $company->id, 404);
        $this->registrations->update($registration, $request->validated());

        return redirect()
            ->route('companies.verbis.index', $company)
            ->with('success', 'VERBİS sicil bilgileri güncellendi.');
    }
}
