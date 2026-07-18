<?php

namespace App\Application\Services\Documents;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Documents\ValueObjects\DocumentPlaceholderMap;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Collection;

class PlaceholderResolver
{
    public function forCompany(Company $company): DocumentPlaceholderMap
    {
        $title = trim((string) ($company->title ?: $company->trade_name));
        $cameraPlaceholders = $this->cameraPlaceholders($company);

        return new DocumentPlaceholderMap([
            'firma_unvani' => $title,
            'ticaret_unvani' => (string) ($company->trade_name ?? ''),
            'adres' => (string) ($company->address ?? ''),
            'mersis' => (string) ($company->mersis_number ?? ''),
            'vergi_no' => (string) ($company->tax_number ?? ''),
            'vergi_dairesi' => (string) ($company->tax_office ?? ''),
            'eposta' => (string) ($company->email ?? ''),
            'telefon' => (string) ($company->phone ?? ''),
            'sehir' => (string) ($company->city ?? ''),
            'ilce' => (string) ($company->district ?? ''),
            'yetkili' => (string) ($company->authorized_person ?? ''),
            'yetkili_unvan' => (string) ($company->authorized_title ?? ''),
            'faaliyet' => (string) ($company->activity_summary ?? ''),
            ...$cameraPlaceholders,
        ]);
    }

    /**
     * @return array{kamera_sayisi: string, kamera_alanlari: string, kamera_saklama_gun: string}
     */
    private function cameraPlaceholders(Company $company): array
    {
        /** @var Collection<int, Camera> $cameras */
        $cameras = $company->relationLoaded('cameras')
            ? $company->cameras
            : $company->cameras()->get();

        $count = $cameras->count();
        $locations = $cameras
            ->map(fn (Camera $camera): string => trim((string) ($camera->location ?: $camera->coverage_area ?: $camera->name)))
            ->filter()
            ->unique()
            ->values();

        $retention = $cameras
            ->pluck('retention_days')
            ->filter(fn ($days): bool => $days !== null && (int) $days > 0)
            ->map(fn ($days): int => (int) $days)
            ->max();

        return [
            'kamera_sayisi' => $count > 0 ? (string) $count : 'belirlenen sayıda',
            'kamera_alanlari' => $locations->isNotEmpty()
                ? $locations->implode(', ')
                : 'işyeri girişleri, ortak alanlar ve güvenlik gerektiren bölgeler',
            'kamera_saklama_gun' => $retention !== null ? (string) $retention : '30',
        ];
    }
}
