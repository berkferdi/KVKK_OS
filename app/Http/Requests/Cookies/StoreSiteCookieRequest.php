<?php

namespace App\Http\Requests\Cookies;

use App\Domain\Cookies\Enums\CookieCategory;
use App\Domain\Cookies\Enums\CookieStatus;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiteCookieRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [SiteCookie::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'name' => ['required', 'string', 'max:255'],
            'cookie_code' => ['nullable', 'string', 'max:64'],
            'category' => ['nullable', Rule::enum(CookieCategory::class)],
            'provider' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string'],
            'duration' => ['nullable', 'string', 'max:255'],
            'duration_days' => ['nullable', 'integer', 'min:0', 'max:36500'],
            'domain' => ['nullable', 'string', 'max:255'],
            'website_id' => [
                'nullable',
                'integer',
                Rule::exists('websites', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
            ],
            'is_third_party' => ['nullable', 'boolean'],
            'requires_consent' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(CookieStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['is_third_party', 'requires_consent'] as $flag) {
            if (! $this->has($flag)) {
                $this->merge([$flag => false]);
            }
        }
    }
}
