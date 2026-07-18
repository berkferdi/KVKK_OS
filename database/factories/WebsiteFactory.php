<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Websites\Enums\WebsiteStatus;
use App\Domain\Websites\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Website>
 */
class WebsiteFactory extends Factory
{
    protected $model = Website::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $domain = fake()->unique()->domainName();

        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => 'Kurumsal Web',
            'website_code' => 'WEB-'.fake()->unique()->numerify('###'),
            'url' => 'https://'.$domain,
            'platform' => 'custom',
            'hosting_provider' => 'Örnek Hosting',
            'has_contact_form' => true,
            'has_newsletter' => false,
            'has_user_accounts' => false,
            'has_payment' => false,
            'ssl_enabled' => true,
            'privacy_policy_published' => true,
            'privacy_policy_url' => 'https://'.$domain.'/kvkk',
            'privacy_policy_published_at' => now()->subMonths(2)->toDateString(),
            'uses_cookies' => true,
            'data_collected' => 'Kimlik, İletişim',
            'notes' => null,
            'source' => 'manual',
            'status' => WebsiteStatus::Active,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Website $website): void {
            if ($website->company_id && empty($website->tenant_id)) {
                $company = Company::query()->find($website->company_id);
                if ($company !== null) {
                    $website->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}
