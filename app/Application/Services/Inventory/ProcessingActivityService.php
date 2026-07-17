<?php

namespace App\Application\Services\Inventory;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Inventory\ProcessingActivityRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProcessingActivityService
{
    public function __construct(
        private readonly ProcessingActivityRepository $activities,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, ProcessingActivity>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->activities->paginateForCompany($company->id, $perPage);
    }

    public function findByUuid(string $uuid): ?ProcessingActivity
    {
        /** @var ProcessingActivity|null $activity */
        $activity = $this->activities->findByUuid($uuid);

        return $activity;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): ProcessingActivity
    {
        $data = $this->normalizeLists($data);
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';

        /** @var ProcessingActivity $activity */
        $activity = $this->activities->create($data);
        $this->auditLogger->log('inventory.created', $activity, null, $activity->toArray(), $company->tenant_id);

        return $activity;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProcessingActivity $activity, array $data): ProcessingActivity
    {
        $old = $activity->toArray();
        $data = $this->normalizeLists($data);

        /** @var ProcessingActivity $updated */
        $updated = $this->activities->update($activity, $data);
        $this->auditLogger->log('inventory.updated', $updated, $old, $updated->toArray(), $updated->tenant_id);

        return $updated;
    }

    public function delete(ProcessingActivity $activity): bool
    {
        $old = $activity->toArray();
        $deleted = $this->activities->delete($activity);
        if ($deleted) {
            $this->auditLogger->log('inventory.deleted', $activity, $old, null, $activity->tenant_id);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeLists(array $data): array
    {
        foreach (['data_categories', 'data_subject_categories', 'recipients'] as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            if (is_string($data[$field])) {
                $data[$field] = array_values(array_filter(array_map(
                    static fn (string $item): string => trim($item),
                    explode(',', $data[$field])
                )));
            }
        }

        return $data;
    }
}
