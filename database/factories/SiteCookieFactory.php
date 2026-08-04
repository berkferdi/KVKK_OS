<?php

namespace Database\Factories;

use App\Domain\Cookies\Enums\CookieCategory;
use App\Domain\Cookies\Enums\CookieStatus;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SiteCookie>
 */
class SiteCookieFactory extends Factory
{
    protected $model = SiteCookie::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => '_ga',
            'cookie_code' => 'CK-'.fake()->unique()->numerify('###'),
            'category' => CookieCategory::Analytics,
            'provider' => 'Google Analytics',
            'purpose' => 'Ziyaretçi istatistikleri',
            'duration' => '2 yıl',
            'duration_days' => 730,
            'domain' => '.ornek.test',
            'is_third_party' => true,
            'requires_consent' => true,
            'notes' => null,
            'source' => 'manual',
            'status' => CookieStatus::Active,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (SiteCookie $cookie): void {
            if ($cookie->company_id && empty($cookie->tenant_id)) {
                $company = Company::query()->find($cookie->company_id);
                if ($company !== null) {
                    $cookie->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}
