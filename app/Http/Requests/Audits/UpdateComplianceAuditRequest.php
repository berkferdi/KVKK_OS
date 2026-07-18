<?php

namespace App\Http\Requests\Audits;

use App\Domain\Audits\Enums\AuditResult;
use App\Domain\Audits\Enums\AuditStatus;
use App\Domain\Audits\Enums\AuditType;
use App\Domain\Audits\Models\ComplianceAudit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComplianceAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ComplianceAudit $audit */
        $audit = $this->route('audit');

        return $this->user()?->can('update', $audit) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var ComplianceAudit $audit */
        $audit = $this->route('audit');

        return [
            'title' => ['required', 'string', 'max:255'],
            'audit_code' => ['nullable', 'string', 'max:64'],
            'audit_type' => ['nullable', Rule::enum(AuditType::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $audit->company_id)),
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
