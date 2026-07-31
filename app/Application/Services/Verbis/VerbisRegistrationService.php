<?php

namespace App\Application\Services\Verbis;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Enums\VerbisRegistrationStatus;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Infrastructure\Repositories\Verbis\VerbisRegistrationRepository;
use Illuminate\Support\Str;

class VerbisRegistrationService
{
    public function __construct(
        private readonly VerbisRegistrationRepository $registrations,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function getOrCreateForCompany(Company $company): VerbisRegistration
    {
        $existing = $this->registrations->findForCompany($company->id);
        if ($existing !== null) {
            return $existing;
        }

        /** @var VerbisRegistration $registration */
        $registration = $this->registrations->create([
            'tenant_id' => $company->tenant_id,
            'company_id' => $company->id,
            'uuid' => (string) Str::uuid(),
            'source' => 'manual',
            'status' => VerbisRegistrationStatus::Draft->value,
            'is_exempt' => false,
            'metadata' => [],
        ]);

        $this->auditLogger->log('verbis.registration.created', $registration, null, [
            'company_id' => $company->id,
        ], $company->tenant_id);

        return $registration;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(VerbisRegistration $registration, array $data): VerbisRegistration
    {
        if (array_key_exists('is_exempt', $data)) {
            $data['is_exempt'] = (bool) $data['is_exempt'];
        }

        $old = [
            'status' => $registration->status instanceof VerbisRegistrationStatus
                ? $registration->status->value
                : null,
            'registration_number' => $registration->registration_number,
        ];

        /** @var VerbisRegistration $updated */
        $updated = $this->registrations->update($registration, $data);
        $this->auditLogger->log('verbis.registration.updated', $updated, $old, [
            'status' => $updated->status instanceof VerbisRegistrationStatus
                ? $updated->status->value
                : null,
            'registration_number' => $updated->registration_number,
        ], $updated->tenant_id);

        return $updated;
    }
}
