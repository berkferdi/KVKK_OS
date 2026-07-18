<?php

namespace App\Application\Services\Breaches;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Breaches\Enums\BreachSeverity;
use App\Domain\Breaches\Enums\BreachStatus;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Breaches\DataBreachRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DataBreachService
{
    public function __construct(
        private readonly DataBreachRepository $breaches,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, DataBreach>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->breaches->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): DataBreach
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['subjects_notification_required'] = (bool) ($data['subjects_notification_required'] ?? false);
        $data = $this->withAuthorityDueDate($data);

        /** @var DataBreach $breach */
        $breach = $this->breaches->create($data);
        $this->auditLogger->log('breach.created', $breach, null, [
            'title' => $breach->title,
            'severity' => $breach->severity instanceof BreachSeverity ? $breach->severity->value : null,
        ], $company->tenant_id);

        return $breach;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(DataBreach $breach, array $data): DataBreach
    {
        if (array_key_exists('subjects_notification_required', $data)) {
            $data['subjects_notification_required'] = (bool) $data['subjects_notification_required'];
        }
        $data = $this->withAuthorityDueDate($data, $breach);

        $old = [
            'title' => $breach->title,
            'status' => $breach->status instanceof BreachStatus ? $breach->status->value : null,
        ];
        /** @var DataBreach $updated */
        $updated = $this->breaches->update($breach, $data);
        $this->auditLogger->log('breach.updated', $updated, $old, [
            'title' => $updated->title,
            'status' => $updated->status instanceof BreachStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(DataBreach $breach): bool
    {
        $old = ['title' => $breach->title];
        $deleted = $this->breaches->delete($breach);
        if ($deleted) {
            $this->auditLogger->log('breach.deleted', $breach, $old, null, $breach->tenant_id);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withAuthorityDueDate(array $data, ?DataBreach $existing = null): array
    {
        if (! empty($data['authority_notification_due_at'])) {
            return $data;
        }

        $discovered = $data['discovered_at'] ?? $existing?->discovered_at;
        if ($discovered === null) {
            return $data;
        }

        if ($existing !== null && $existing->authority_notification_due_at !== null && ! array_key_exists('discovered_at', $data)) {
            return $data;
        }

        $data['authority_notification_due_at'] = Carbon::parse($discovered)->addHours(72)->toDateTimeString();

        return $data;
    }
}
