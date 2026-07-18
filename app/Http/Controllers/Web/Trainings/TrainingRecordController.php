<?php

namespace App\Http\Controllers\Web\Trainings;

use App\Application\Services\Trainings\TrainingRecordService;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Trainings\Enums\TrainingDeliveryMethod;
use App\Domain\Trainings\Enums\TrainingStatus;
use App\Domain\Trainings\Enums\TrainingType;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trainings\StoreTrainingRecordRequest;
use App\Http\Requests\Trainings\UpdateTrainingRecordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrainingRecordController extends Controller
{
    public function __construct(
        private readonly TrainingRecordService $trainings,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', TrainingRecord::class);

        return view('trainings.index', [
            'company' => $company,
            'trainings' => $this->trainings->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [TrainingRecord::class, $company]);

        return view('trainings.create', [
            'company' => $company,
            'trainingTypes' => TrainingType::cases(),
            'deliveryMethods' => TrainingDeliveryMethod::cases(),
            'statuses' => TrainingStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreTrainingRecordRequest $request, Company $company): RedirectResponse
    {
        $training = $this->trainings->create($company, $request->validated());

        return redirect()
            ->route('companies.trainings.show', [$company, $training])
            ->with('success', 'Eğitim kaydı oluşturuldu.');
    }

    public function show(Company $company, TrainingRecord $training): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $training);
        abort_unless($training->company_id === $company->id, 404);
        $training->load('branch');

        return view('trainings.show', compact('company', 'training'));
    }

    public function edit(Company $company, TrainingRecord $training): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $training);
        abort_unless($training->company_id === $company->id, 404);

        return view('trainings.edit', [
            'company' => $company,
            'training' => $training,
            'trainingTypes' => TrainingType::cases(),
            'deliveryMethods' => TrainingDeliveryMethod::cases(),
            'statuses' => TrainingStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateTrainingRecordRequest $request,
        Company $company,
        TrainingRecord $training,
    ): RedirectResponse {
        abort_unless($training->company_id === $company->id, 404);
        $this->trainings->update($training, $request->validated());

        return redirect()
            ->route('companies.trainings.show', [$company, $training])
            ->with('success', 'Eğitim kaydı güncellendi.');
    }

    public function destroy(Company $company, TrainingRecord $training): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $training);
        abort_unless($training->company_id === $company->id, 404);
        $this->trainings->delete($training);

        return redirect()
            ->route('companies.trainings.index', $company)
            ->with('success', 'Eğitim kaydı silindi.');
    }
}
