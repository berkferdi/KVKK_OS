<?php

namespace Database\Factories;

use App\Domain\Documents\Enums\PackageStatus;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DeliveryPackage>
 */
class DeliveryPackageFactory extends Factory
{
    protected $model = DeliveryPackage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Teslim paketi',
            'status' => PackageStatus::Ready,
            'file_path' => null,
            'file_size' => null,
            'document_count' => 0,
            'folder_snapshot' => [],
            'error_message' => null,
            'version' => 1,
            'generated_at' => now(),
            'generated_by' => null,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (DeliveryPackage $package): void {
            if ($package->company_id && empty($package->tenant_id)) {
                $company = Company::query()->find($package->company_id);
                if ($company !== null) {
                    $package->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}
