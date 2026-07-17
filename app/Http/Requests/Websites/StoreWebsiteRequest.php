<?php

namespace App\Http\Requests\Websites;

use App\Domain\Organization\Models\Company;
use App\Domain\Websites\Enums\WebsiteStatus;
use App\Domain\Websites\Models\Website;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [Website::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website_code' => ['nullable', 'string', 'max:64'],
            'url' => ['required', 'url', 'max:255'],
            'platform' => ['nullable', 'string', 'max:64'],
            'hosting_provider' => ['nullable', 'string', 'max:255'],
            'has_contact_form' => ['nullable', 'boolean'],
            'has_newsletter' => ['nullable', 'boolean'],
            'has_user_accounts' => ['nullable', 'boolean'],
            'has_payment' => ['nullable', 'boolean'],
            'ssl_enabled' => ['nullable', 'boolean'],
            'privacy_policy_published' => ['nullable', 'boolean'],
            'privacy_policy_url' => ['nullable', 'url', 'max:255'],
            'privacy_policy_published_at' => ['nullable', 'date'],
            'uses_cookies' => ['nullable', 'boolean'],
            'data_collected' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(WebsiteStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'has_contact_form',
            'has_newsletter',
            'has_user_accounts',
            'has_payment',
            'ssl_enabled',
            'privacy_policy_published',
            'uses_cookies',
        ] as $flag) {
            if (! $this->has($flag)) {
                $this->merge([$flag => false]);
            }
        }
    }
}
