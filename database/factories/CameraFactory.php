<?php

namespace Database\Factories;

use App\Domain\Cameras\Enums\CameraStatus;
use App\Domain\Cameras\Enums\CameraType;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Camera>
 */
class CameraFactory extends Factory
{
    protected $model = Camera::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => 'Giriş Kamerası',
            'camera_code' => 'CAM-'.fake()->unique()->numerify('###'),
            'camera_type' => CameraType::Indoor,
            'location' => 'Ana giriş',
            'coverage_area' => 'Resepsiyon ve giriş holü',
            'is_recording' => true,
            'records_audio' => false,
            'retention_days' => 30,
            'storage_location' => 'NVR',
            'notice_posted' => true,
            'notice_posted_at' => now()->subMonths(3)->toDateString(),
            'installed_at' => now()->subYear()->toDateString(),
            'notes' => null,
            'source' => 'manual',
            'status' => CameraStatus::Active,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Camera $camera): void {
            if ($camera->company_id && empty($camera->tenant_id)) {
                $company = Company::query()->find($camera->company_id);
                if ($company !== null) {
                    $camera->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}
