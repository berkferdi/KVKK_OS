<?php

namespace App\Application\Services\Applications;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Applications\Enums\ApplicationRequestType;
use App\Domain\Applications\Enums\ApplicationStatus;
use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Applications\DataSubjectApplicationRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DataSubjectApplicationService
{
    public function __construct(
        private readonly DataSubjectApplicationRepository $applications,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, DataSubjectApplication>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applications->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): DataSubjectApplication
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['identity_verified'] = (bool) ($data['identity_verified'] ?? false);
        $data = $this->withDefaultDueDate($data);

        /** @var DataSubjectApplication $application */
        $application = $this->applications->create($data);
        $this->auditLogger->log('application.created', $application, null, [
            'applicant_name' => $application->applicant_name,
            'request_type' => $application->request_type instanceof ApplicationRequestType
                ? $application->request_type->value
                : null,
        ], $company->tenant_id);

        return $application;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(DataSubjectApplication $application, array $data): DataSubjectApplication
    {
        if (array_key_exists('identity_verified', $data)) {
            $data['identity_verified'] = (bool) $data['identity_verified'];
        }
        $data = $this->withDefaultDueDate($data, $application);

        $old = [
            'status' => $application->status instanceof ApplicationStatus ? $application->status->value : null,
            'applicant_name' => $application->applicant_name,
        ];
        /** @var DataSubjectApplication $updated */
        $updated = $this->applications->update($application, $data);
        $this->auditLogger->log('application.updated', $updated, $old, [
            'status' => $updated->status instanceof ApplicationStatus ? $updated->status->value : null,
            'applicant_name' => $updated->applicant_name,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(DataSubjectApplication $application): bool
    {
        $old = ['applicant_name' => $application->applicant_name];
        $deleted = $this->applications->delete($application);
        if ($deleted) {
            $this->auditLogger->log('application.deleted', $application, $old, null, $application->tenant_id);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withDefaultDueDate(array $data, ?DataSubjectApplication $existing = null): array
    {
        if (! empty($data['due_at'])) {
            return $data;
        }

        $received = $data['received_at'] ?? $existing?->received_at;
        if ($received === null) {
            return $data;
        }

        if ($existing !== null && $existing->due_at !== null && ! array_key_exists('received_at', $data)) {
            return $data;
        }

        $data['due_at'] = Carbon::parse($received)->addDays(30)->toDateString();

        return $data;
    }
}
