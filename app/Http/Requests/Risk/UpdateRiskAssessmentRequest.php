<?php

namespace App\Http\Requests\Risk;

use App\Domain\Risk\Enums\RiskAssessmentStatus;
use App\Domain\Risk\Models\RiskAssessment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRiskAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var RiskAssessment $risk */
        $risk = $this->route('risk');

        return $this->user()?->can('update', $risk) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var RiskAssessment $risk */
        $risk = $this->route('risk');

        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
            'processing_activity_id' => [
                'nullable',
                'integer',
                Rule::exists('processing_activities', 'id')->where(fn ($q) => $q->where('company_id', $risk->company_id)),
            ],
            'asset_type' => ['nullable', 'string', 'max:64'],
            'threat' => ['nullable', 'string', 'max:255'],
            'vulnerability' => ['nullable', 'string', 'max:255'],
            'likelihood' => ['required', 'integer', 'min:1', 'max:5'],
            'impact' => ['required', 'integer', 'min:1', 'max:5'],
            'existing_controls' => ['nullable', 'string'],
            'mitigation_plan' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'review_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::enum(RiskAssessmentStatus::class)],
        ];
    }
}
