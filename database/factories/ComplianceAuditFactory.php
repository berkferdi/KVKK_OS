<?php

namespace Database\Factories;

use App\Domain\Audits\Enums\AuditResult;
use App\Domain\Audits\Enums\AuditStatus;
use App\Domain\Audits\Enums\AuditType;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ComplianceAudit>
 */
class ComplianceAuditFactory extends Factory
{
    protected $model = ComplianceAudit::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planned = now()->addDays(14);

        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Yıllık KVKK iç denetimi',
            'audit_code' => 'DNT-'.fake()->unique()->numerify('####'),
            'audit_type' => AuditType::Internal,
            'planned_at' => $planned,
            'started_at' => null,
            'completed_at' => null,
            'next_audit_due_at' => null,
            'auditor_name' => fake()->name(),
            'scope' => 'Kamera sistemleri ve personel dosyaları',
            'findings' => null,
            'recommendations' => null,
            'corrective_actions' => null,
            'notes' => null,
            'source' => 'manual',
            'result' => AuditResult::Pending,
            'status' => AuditStatus::Planned,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ComplianceAudit $audit): void {
            if ($audit->company_id && empty($audit->tenant_id)) {
                $company = Company::query()->find($audit->company_id);
                if ($company !== null) {
                    $audit->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}
