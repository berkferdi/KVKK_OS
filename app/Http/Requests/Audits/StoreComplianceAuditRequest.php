<?php

namespace App\Http\Requests\Audits;

use App\Domain\Audits\Enums\AuditResult;
use App\Domain\Audits\Enums\AuditStatus;
use App\Domain\Audits\Enums\AuditType;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplianceAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [ComplianceAudit::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'title' => ['required', 'string', 'max:255'],
            'audit_code' => ['nullable', 'string', 'max:64'],
            'audit_type' => ['nullable', Rule::enum(AuditType::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
            ],
            'planned_at' => ['nullable', 'date'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'next_audit_due_at' => ['nullable', 'date'],
            'auditor_name' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string'],
            'findings' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'corrective_actions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'result' => ['nullable', Rule::enum(AuditResult::class)],
            'status' => ['nullable', Rule::enum(AuditStatus::class)],
        ];
    }
}
