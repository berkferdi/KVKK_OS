<?php

namespace App\Http\Requests\Breaches;

use App\Domain\Breaches\Enums\BreachSeverity;
use App\Domain\Breaches\Enums\BreachStatus;
use App\Domain\Breaches\Enums\BreachType;
use App\Domain\Breaches\Models\DataBreach;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataBreachRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var DataBreach $breach */
        $breach = $this->route('breach');

        return $this->user()?->can('update', $breach) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var DataBreach $breach */
        $breach = $this->route('breach');

        return [
            'title' => ['required', 'string', 'max:255'],
            'breach_code' => ['nullable', 'string', 'max:64'],
            'breach_type' => ['nullable', Rule::enum(BreachType::class)],
            'severity' => ['nullable', Rule::enum(BreachSeverity::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $breach->company_id)),
            ],
            'discovered_at' => ['nullable', 'date'],
            'occurred_at' => ['nullable', 'date'],
            'authority_notification_due_at' => ['nullable', 'date'],
            'authority_notified_at' => ['nullable', 'date'],
            'subjects_notification_required' => ['nullable', 'boolean'],
            'subjects_notified_at' => ['nullable', 'date'],
            'affected_subjects_count' => ['nullable', 'integer', 'min:0'],
            'data_categories' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'consequences' => ['nullable', 'string'],
            'measures_taken' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'assigned_to_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(BreachStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('subjects_notification_required')) {
            $this->merge(['subjects_notification_required' => false]);
        }
    }
}
