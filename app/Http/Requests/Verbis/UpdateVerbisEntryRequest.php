<?php

namespace App\Http\Requests\Verbis;

use App\Domain\Verbis\Enums\VerbisEntryStatus;
use App\Domain\Verbis\Models\VerbisEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVerbisEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var VerbisEntry $entry */
        $entry = $this->route('entry');

        return $this->user()?->can('update', $entry) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var VerbisEntry $entry */
        $entry = $this->route('entry');

        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'purposes' => ['nullable', 'string'],
            'data_subject_categories' => ['nullable', 'string'],
            'data_categories' => ['nullable', 'string'],
            'legal_basis' => ['nullable', 'string', 'max:255'],
            'recipients' => ['nullable', 'string'],
            'retention_period' => ['nullable', 'string', 'max:255'],
            'cross_border_transfer' => ['nullable', 'boolean'],
            'transfer_countries' => ['nullable', 'string', 'max:255'],
            'security_measures' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'processing_activity_id' => [
                'nullable',
                'integer',
                Rule::exists('processing_activities', 'id')->where(fn ($q) => $q->where('company_id', $entry->company_id)),
            ],
            'status' => ['nullable', Rule::enum(VerbisEntryStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('cross_border_transfer')) {
            $this->merge(['cross_border_transfer' => false]);
        }
    }
}
