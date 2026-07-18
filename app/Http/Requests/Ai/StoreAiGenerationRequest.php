<?php

namespace App\Http\Requests\Ai;

use App\Domain\Ai\Enums\AiPurpose;
use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAiGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [AiGeneration::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'purpose' => ['required', Rule::enum(AiPurpose::class)],
            'document_template_id' => [
                'nullable',
                'required_if:purpose,document_draft',
                'integer',
                Rule::exists('document_templates', 'id')->where(
                    fn ($q) => $q->where('tenant_id', $company->tenant_id)->where('is_active', true)
                ),
            ],
            'analysis_run_id' => [
                'nullable',
                'required_if:purpose,findings_summary',
                'integer',
                Rule::exists('analysis_runs', 'id')->where(
                    fn ($q) => $q->where('company_id', $company->id)
                ),
            ],
        ];
    }
}
