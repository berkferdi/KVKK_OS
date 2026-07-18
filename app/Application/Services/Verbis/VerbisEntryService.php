<?php

namespace App\Application\Services\Verbis;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Enums\VerbisEntryStatus;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Infrastructure\Repositories\Verbis\VerbisEntryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VerbisEntryService
{
    public function __construct(
        private readonly VerbisEntryRepository $entries,
        private readonly VerbisRegistrationService $registrations,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, VerbisEntry>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->entries->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): VerbisEntry
    {
        $registration = $this->registrations->getOrCreateForCompany($company);

        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['verbis_registration_id'] = $registration->id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['cross_border_transfer'] = (bool) ($data['cross_border_transfer'] ?? false);

        /** @var VerbisEntry $entry */
        $entry = $this->entries->create($data);
        $this->auditLogger->log('verbis.entry.created', $entry, null, [
            'title' => $entry->title,
            'code' => $entry->code,
        ], $company->tenant_id);

        return $entry;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(VerbisEntry $entry, array $data): VerbisEntry
    {
        if (array_key_exists('cross_border_transfer', $data)) {
            $data['cross_border_transfer'] = (bool) $data['cross_border_transfer'];
        }

        $old = [
            'title' => $entry->title,
            'status' => $entry->status instanceof VerbisEntryStatus ? $entry->status->value : null,
        ];
        /** @var VerbisEntry $updated */
        $updated = $this->entries->update($entry, $data);
        $this->auditLogger->log('verbis.entry.updated', $updated, $old, [
            'title' => $updated->title,
            'status' => $updated->status instanceof VerbisEntryStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(VerbisEntry $entry): bool
    {
        $old = ['title' => $entry->title];
        $deleted = $this->entries->delete($entry);
        if ($deleted) {
            $this->auditLogger->log('verbis.entry.deleted', $entry, $old, null, $entry->tenant_id);
        }

        return $deleted;
    }
}
