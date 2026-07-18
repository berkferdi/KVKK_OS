<?php

namespace App\Application\Services\Trainings;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Trainings\Enums\TrainingStatus;
use App\Domain\Trainings\Enums\TrainingType;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Infrastructure\Repositories\Trainings\TrainingRecordRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrainingRecordService
{
    public function __construct(
        private readonly TrainingRecordRepository $trainings,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, TrainingRecord>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->trainings->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): TrainingRecord
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data = $this->withNextTrainingDueDate($data);

        /** @var TrainingRecord $training */
        $training = $this->trainings->create($data);
        $this->auditLogger->log('training.created', $training, null, [
            'title' => $training->title,
            'training_type' => $training->training_type instanceof TrainingType ? $training->training_type->value : null,
        ], $company->tenant_id);

        return $training;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TrainingRecord $training, array $data): TrainingRecord
    {
        $data = $this->withNextTrainingDueDate($data, $training);

        $old = [
            'title' => $training->title,
            'status' => $training->status instanceof TrainingStatus ? $training->status->value : null,
        ];
        /** @var TrainingRecord $updated */
        $updated = $this->trainings->update($training, $data);
        $this->auditLogger->log('training.updated', $updated, $old, [
            'title' => $updated->title,
            'status' => $updated->status instanceof TrainingStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(TrainingRecord $training): bool
    {
        $old = ['title' => $training->title];
        $deleted = $this->trainings->delete($training);
        if ($deleted) {
            $this->auditLogger->log('training.deleted', $training, $old, null, $training->tenant_id);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withNextTrainingDueDate(array $data, ?TrainingRecord $existing = null): array
    {
        if (! empty($data['next_training_due_at'])) {
            return $data;
        }

        $status = $data['status'] ?? $existing?->status;
        $statusValue = $status instanceof TrainingStatus ? $status->value : (string) $status;

        if ($statusValue !== TrainingStatus::Completed->value) {
            return $data;
        }

        if ($existing !== null && $existing->next_training_due_at !== null && ! array_key_exists('status', $data) && ! array_key_exists('conducted_at', $data)) {
            return $data;
        }

        $conducted = $data['conducted_at'] ?? ($existing !== null ? $existing->conducted_at : null) ?? now();
        $data['conducted_at'] = $data['conducted_at'] ?? Carbon::parse($conducted)->toDateTimeString();
        $data['next_training_due_at'] = Carbon::parse($conducted)->addYear()->toDateTimeString();

        return $data;
    }
}
