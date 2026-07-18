<?php

namespace App\Application\Services\Cameras;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Cameras\Enums\CameraStatus;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Cameras\CameraRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CameraService
{
    public function __construct(
        private readonly CameraRepository $cameras,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Camera>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->cameras->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Camera
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['is_recording'] = (bool) ($data['is_recording'] ?? false);
        $data['records_audio'] = (bool) ($data['records_audio'] ?? false);
        $data['notice_posted'] = (bool) ($data['notice_posted'] ?? false);

        /** @var Camera $camera */
        $camera = $this->cameras->create($data);
        $this->auditLogger->log('camera.created', $camera, null, [
            'name' => $camera->name,
            'camera_code' => $camera->camera_code,
        ], $company->tenant_id);

        return $camera;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Camera $camera, array $data): Camera
    {
        foreach (['is_recording', 'records_audio', 'notice_posted'] as $flag) {
            if (array_key_exists($flag, $data)) {
                $data[$flag] = (bool) $data[$flag];
            }
        }

        $old = [
            'name' => $camera->name,
            'status' => $camera->status instanceof CameraStatus ? $camera->status->value : null,
        ];
        /** @var Camera $updated */
        $updated = $this->cameras->update($camera, $data);
        $this->auditLogger->log('camera.updated', $updated, $old, [
            'name' => $updated->name,
            'status' => $updated->status instanceof CameraStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(Camera $camera): bool
    {
        $old = ['name' => $camera->name];
        $deleted = $this->cameras->delete($camera);
        if ($deleted) {
            $this->auditLogger->log('camera.deleted', $camera, $old, null, $camera->tenant_id);
        }

        return $deleted;
    }
}
