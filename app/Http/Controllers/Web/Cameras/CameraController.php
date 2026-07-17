<?php

namespace App\Http\Controllers\Web\Cameras;

use App\Application\Services\Cameras\CameraService;
use App\Domain\Cameras\Enums\CameraStatus;
use App\Domain\Cameras\Enums\CameraType;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cameras\StoreCameraRequest;
use App\Http\Requests\Cameras\UpdateCameraRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CameraController extends Controller
{
    public function __construct(
        private readonly CameraService $cameras,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Camera::class);

        return view('cameras.index', [
            'company' => $company,
            'cameras' => $this->cameras->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [Camera::class, $company]);

        return view('cameras.create', [
            'company' => $company,
            'cameraTypes' => CameraType::cases(),
            'statuses' => CameraStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreCameraRequest $request, Company $company): RedirectResponse
    {
        $camera = $this->cameras->create($company, $request->validated());

        return redirect()
            ->route('companies.cameras.show', [$company, $camera])
            ->with('success', 'Kamera kaydı oluşturuldu.');
    }

    public function show(Company $company, Camera $camera): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $camera);
        abort_unless($camera->company_id === $company->id, 404);
        $camera->load('branch');

        return view('cameras.show', compact('company', 'camera'));
    }

    public function edit(Company $company, Camera $camera): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $camera);
        abort_unless($camera->company_id === $company->id, 404);

        return view('cameras.edit', [
            'company' => $company,
            'camera' => $camera,
            'cameraTypes' => CameraType::cases(),
            'statuses' => CameraStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateCameraRequest $request,
        Company $company,
        Camera $camera,
    ): RedirectResponse {
        abort_unless($camera->company_id === $company->id, 404);
        $this->cameras->update($camera, $request->validated());

        return redirect()
            ->route('companies.cameras.show', [$company, $camera])
            ->with('success', 'Kamera kaydı güncellendi.');
    }

    public function destroy(Company $company, Camera $camera): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $camera);
        abort_unless($camera->company_id === $company->id, 404);
        $this->cameras->delete($camera);

        return redirect()
            ->route('companies.cameras.index', $company)
            ->with('success', 'Kamera kaydı silindi.');
    }
}
