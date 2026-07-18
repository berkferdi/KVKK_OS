<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Enums\PackageStatus;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Documents\DeliveryZipBuilder;
use App\Infrastructure\Repositories\Documents\DeliveryPackageRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DeliveryPackageService
{
    public function __construct(
        private readonly DeliveryPackageRepository $packages,
        private readonly DeliveryZipBuilder $zipBuilder,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, DeliveryPackage>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->packages->paginateForCompany($company->id, $perPage);
    }

    public function create(Company $company, ?User $user = null): DeliveryPackage
    {
        $documents = GeneratedDocument::query()
            ->where('company_id', $company->id)
            ->where('status', GenerationStatus::Generated->value)
            ->with('template')
            ->orderBy('code')
            ->get();

        if ($documents->isEmpty()) {
            throw new InvalidArgumentException('Teslim paketi için en az bir başarılı üretilmiş belge gerekir.');
        }

        $version = $this->packages->nextVersionForCompany($company->id);
        $relativePath = sprintf(
            'packages/%d/%d/%s_v%d.zip',
            (int) $company->tenant_id,
            (int) $company->id,
            $company->uuid,
            $version,
        );

        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kvkk_'.uniqid('zip_', true).'.zip';

        try {
            $built = $this->zipBuilder->build($company, $documents, $tempPath);
            $binary = file_get_contents($tempPath);
            if ($binary === false) {
                throw new RuntimeException('ZIP dosyası okunamadı.');
            }

            Storage::disk('local')->put($relativePath, $binary);
            $this->packages->supersedeReadyForCompany($company->id);

            /** @var DeliveryPackage $package */
            $package = $this->packages->create([
                'tenant_id' => $company->tenant_id,
                'company_id' => $company->id,
                'title' => sprintf('Teslim paketi v%d', $version),
                'status' => PackageStatus::Ready,
                'file_path' => $relativePath,
                'file_size' => strlen($binary),
                'document_count' => $built['document_count'],
                'folder_snapshot' => $built['relative_entries'],
                'version' => $version,
                'generated_at' => now(),
                'generated_by' => $user?->id,
                'metadata' => [
                    'generated_document_ids' => $documents->pluck('id')->values()->all(),
                ],
            ]);

            $this->auditLogger->log('delivery_package.created', $package, null, [
                'version' => $package->version,
                'document_count' => $package->document_count,
                'file_path' => $package->file_path,
            ], $company->tenant_id);

            return $package;
        } catch (Throwable $e) {
            /** @var DeliveryPackage $failed */
            $failed = $this->packages->create([
                'tenant_id' => $company->tenant_id,
                'company_id' => $company->id,
                'title' => sprintf('Teslim paketi v%d', $version),
                'status' => PackageStatus::Failed,
                'document_count' => 0,
                'folder_snapshot' => [],
                'error_message' => $e->getMessage(),
                'version' => $version,
                'generated_at' => now(),
                'generated_by' => $user?->id,
                'metadata' => [],
            ]);

            $this->auditLogger->log('delivery_package.failed', $failed, null, [
                'error' => $e->getMessage(),
            ], $company->tenant_id);

            throw $e;
        } finally {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    public function download(DeliveryPackage $package): StreamedResponse
    {
        if (! $package->isDownloadable()) {
            throw new InvalidArgumentException('Bu paket indirilemez.');
        }

        $path = (string) $package->file_path;
        if (! Storage::disk('local')->exists($path)) {
            throw new RuntimeException('ZIP dosyası bulunamadı.');
        }

        $this->auditLogger->log('delivery_package.downloaded', $package, null, [
            'file_path' => $path,
        ], $package->tenant_id);

        return Storage::disk('local')->download(
            $path,
            $this->downloadFilename($package),
            ['Content-Type' => 'application/zip'],
        );
    }

    public function delete(DeliveryPackage $package): bool
    {
        if ($package->file_path && Storage::disk('local')->exists($package->file_path)) {
            Storage::disk('local')->delete($package->file_path);
        }

        $old = ['title' => $package->title, 'version' => $package->version];
        $deleted = $this->packages->delete($package);
        if ($deleted) {
            $this->auditLogger->log('delivery_package.deleted', $package, $old, null, $package->tenant_id);
        }

        return $deleted;
    }

    public function downloadFilename(DeliveryPackage $package): string
    {
        return sprintf('teslim_paketi_v%d.zip', $package->version);
    }
}
