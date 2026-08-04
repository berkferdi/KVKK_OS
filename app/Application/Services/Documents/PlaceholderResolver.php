<?php

namespace App\Application\Services\Documents;

use App\Domain\Documents\ValueObjects\DocumentPlaceholderMap;
use App\Domain\Organization\Models\Company;

class PlaceholderResolver
{
    public function forCompany(Company $company): DocumentPlaceholderMap
    {
        $title = trim((string) ($company->title ?: $company->trade_name));

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
        ]);
    }
}
