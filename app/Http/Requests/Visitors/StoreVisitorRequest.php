<?php

namespace App\Http\Requests\Visitors;

use App\Domain\Organization\Models\Company;
use App\Domain\Visitors\Enums\VisitorStatus;
use App\Domain\Visitors\Models\Visitor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [Visitor::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'visitor_code' => ['nullable', 'string', 'max:64'],
            'organization' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'host_name' => ['nullable', 'string', 'max:255'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
            ],
            'visited_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date', 'after_or_equal:visited_at'],
            'privacy_notice_signed_at' => ['nullable', 'date'],
            'photo_captured' => ['nullable', 'boolean'],
            'badge_issued' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(VisitorStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('photo_captured')) {
            $this->merge(['photo_captured' => false]);
        }
        if (! $this->has('badge_issued')) {
            $this->merge(['badge_issued' => false]);
        }
    }
}
