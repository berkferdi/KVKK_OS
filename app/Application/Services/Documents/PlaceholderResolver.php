<?php

namespace App\Application\Services\Documents;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Documents\Support\PlaceholderCatalog;
use App\Domain\Documents\ValueObjects\DocumentPlaceholderMap;
use App\Domain\Organization\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;

class PlaceholderResolver
{
    /**
     * @param  array<string, string>  $documentMeta
     */
    public function forCompany(Company $company, array $documentMeta = [], ?User $user = null): DocumentPlaceholderMap
    {
        $title = trim((string) ($company->title ?: $company->trade_name));
        $address = (string) ($company->address ?? '');
        $email = (string) ($company->email ?? '');
        $phone = (string) ($company->phone ?? '');
        $kvkkEmail = (string) ($company->kvkk_email ?: $email);
        $kep = (string) ($company->kep_address ?? '');
        $camera = $this->cameraPlaceholders($company);

        $values = [
            'firma_unvani' => $title,
            'ticaret_unvani' => $this->fallback((string) ($company->trade_name ?? ''), $title),
            'vergi_no' => $this->optional((string) ($company->tax_number ?? '')),
            'vergi_dairesi' => $this->optional((string) ($company->tax_office ?? '')),
            'mersis' => $this->optional((string) ($company->mersis_number ?? '')),
            'adres' => $this->optional($address),
            'ilce' => $this->optional((string) ($company->district ?? '')),
            'sehir' => $this->optional((string) ($company->city ?? '')),
            'posta_kodu' => $this->optional((string) ($company->postal_code ?? '')),
            'ulke' => $this->fallback((string) ($company->country ?? ''), 'Türkiye'),
            'telefon' => $this->optional($phone),
            'eposta' => $this->optional($email),
            'web' => $this->optional((string) ($company->website_url ?? '')),
            'kep' => $this->optional($kep),
            'kvkk_eposta' => $this->fallback($kvkkEmail, $this->optional($email)),
            'yetkili' => $this->optional((string) ($company->authorized_person ?? '')),
            'yetkili_unvan' => $this->optional((string) ($company->authorized_title ?? '')),
            'faaliyet' => $this->optional((string) ($company->activity_summary ?? '')),
            'nace_kodu' => $this->optional((string) ($company->nace_code ?? '')),
            'kurulus_tarihi' => $company->founded_at
                ? $company->founded_at->format('d.m.Y')
                : '—',
            'calisan_sayisi' => $company->employee_count !== null
                ? (string) $company->employee_count
                : '—',
            'sgk_sicil_no' => $this->optional((string) ($company->sgk_registration_number ?? '')),
            'ticaret_sicil_no' => $this->optional((string) ($company->trade_registry_number ?? '')),
            ...$camera,
            'veri_sorumlusu' => $this->fallback($title, '—'),
            'veri_sorumlusu_adres' => $this->fallback($address, '—'),
            'veri_sorumlusu_eposta' => $this->fallback($kvkkEmail !== '' ? $kvkkEmail : $email, '—'),
            'veri_sorumlusu_telefon' => $this->fallback($phone, '—'),
            'kvkk_basvuru_adresi' => $this->fallback($address, '—'),
            'kvkk_basvuru_eposta' => $this->fallback($kvkkEmail !== '' ? $kvkkEmail : $email, '—'),
            'kvkk_basvuru_kep' => $this->fallback($kep, '—'),
            'dokuman_no' => $this->optional($documentMeta['dokuman_no'] ?? ''),
            'versiyon' => $this->fallback($documentMeta['versiyon'] ?? '', '1'),
            'revizyon_no' => $this->fallback($documentMeta['revizyon_no'] ?? '', '00'),
            'revizyon_tarihi' => $this->fallback($documentMeta['revizyon_tarihi'] ?? '', now()->format('d.m.Y')),
            'yayin_tarihi' => $this->fallback($documentMeta['yayin_tarihi'] ?? '', now()->format('d.m.Y')),
            'onaylayan' => $this->fallback(
                $documentMeta['onaylayan'] ?? '',
                $this->fallback((string) ($company->authorized_person ?? ''), '—'),
            ),
            'hazirlayan' => $this->fallback(
                $documentMeta['hazirlayan'] ?? '',
                $this->fallback((string) ($user?->name ?? ''), '—'),
            ),
            'dokuman_baslik' => $this->optional($documentMeta['dokuman_baslik'] ?? ''),
            'yururluk_durumu' => $this->fallback($documentMeta['yururluk_durumu'] ?? '', 'Yürürlükte'),
            'ise_giris_tarihi' => $this->optional($documentMeta['ise_giris_tarihi'] ?? ''),
            'departman' => $this->optional($documentMeta['departman'] ?? ''),
            'pozisyon' => $this->optional($documentMeta['pozisyon'] ?? ''),
            'tedarikci_unvani' => $this->optional($documentMeta['tedarikci_unvani'] ?? ''),
            'tedarikci_yetkili' => $this->optional($documentMeta['tedarikci_yetkili'] ?? ''),
        ];

        // Katalogda olup map'te olmayan anahtar kalmasın.
        foreach (array_keys(PlaceholderCatalog::definitions()) as $key) {
            if (! array_key_exists($key, $values)) {
                $values[$key] = '—';
            }
        }

        return new DocumentPlaceholderMap($values);
    }

    /**
     * @return array{kamera_sayisi: string, kamera_alanlari: string, kamera_saklama_gun: string, kamera_amaci: string}
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
            'kamera_amaci' => 'İşyeri, çalışan, ziyaretçi ve tesis güvenliğinin sağlanması',
        ];
    }

    private function optional(string $value): string
    {
        $value = trim($value);

        return $value !== '' ? $value : '—';
    }

    private function fallback(string $value, string $fallback): string
    {
        $value = trim($value);

        return $value !== '' ? $value : $fallback;
    }
}
