<?php

namespace App\Http\Controllers\Web\Applications;

use App\Application\Services\Applications\DataSubjectApplicationService;
use App\Domain\Applications\Enums\ApplicationChannel;
use App\Domain\Applications\Enums\ApplicationRequestType;
use App\Domain\Applications\Enums\ApplicationStatus;
use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\StoreDataSubjectApplicationRequest;
use App\Http\Requests\Applications\UpdateDataSubjectApplicationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DataSubjectApplicationController extends Controller
{
    public function __construct(
        private readonly DataSubjectApplicationService $applications,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', DataSubjectApplication::class);

        return view('applications.index', [
            'company' => $company,
            'applications' => $this->applications->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [DataSubjectApplication::class, $company]);

        return view('applications.create', [
            'company' => $company,
            'requestTypes' => ApplicationRequestType::cases(),
            'channels' => ApplicationChannel::cases(),
            'statuses' => ApplicationStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreDataSubjectApplicationRequest $request, Company $company): RedirectResponse
    {
        $application = $this->applications->create($company, $request->validated());

        return redirect()
            ->route('companies.applications.show', [$company, $application])
            ->with('success', 'Başvuru kaydı oluşturuldu.');
    }

    public function show(Company $company, DataSubjectApplication $application): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $application);
        abort_unless($application->company_id === $company->id, 404);
        $application->load('branch');

        return view('applications.show', compact('company', 'application'));
    }

    public function edit(Company $company, DataSubjectApplication $application): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $application);
        abort_unless($application->company_id === $company->id, 404);

        return view('applications.edit', [
            'company' => $company,
            'application' => $application,
            'requestTypes' => ApplicationRequestType::cases(),
            'channels' => ApplicationChannel::cases(),
            'statuses' => ApplicationStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateDataSubjectApplicationRequest $request,
        Company $company,
        DataSubjectApplication $application,
    ): RedirectResponse {
        abort_unless($application->company_id === $company->id, 404);
        $this->applications->update($application, $request->validated());

        return redirect()
            ->route('companies.applications.show', [$company, $application])
            ->with('success', 'Başvuru kaydı güncellendi.');
    }

    public function destroy(Company $company, DataSubjectApplication $application): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $application);
        abort_unless($application->company_id === $company->id, 404);
        $this->applications->delete($application);

        return redirect()
            ->route('companies.applications.index', $company)
            ->with('success', 'Başvuru kaydı silindi.');
    }
}
