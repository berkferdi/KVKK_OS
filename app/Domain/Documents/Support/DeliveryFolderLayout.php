<?php

namespace App\Domain\Documents\Support;

use App\Domain\Documents\Enums\TemplateCategory;

final class DeliveryFolderLayout
{
    /**
     * @return list<string>
     */
    public static function folders(): array
    {
        return [
            '01 Kurumsal Belgeler',
            '02 Politikalar',
            '03 Prosedürler',
            '04 Envanter',
            '05 Risk',
            '06 Personel',
            '07 Müşteri',
            '08 Tedarikçi',
            '09 Kamera',
            '10 Web',
            '11 VERBİS',
            '12 Eğitim',
            '13 Denetim',
            '14 İmzalı Belgeler',
            '15 Teslim Dosyası',
        ];
    }

    public static function folderForCategory(?TemplateCategory $category): string
    {
        return match ($category) {
            TemplateCategory::Policy => '02 Politikalar',
            TemplateCategory::Camera => '09 Kamera',
            TemplateCategory::Web, TemplateCategory::Cookie => '10 Web',
            TemplateCategory::Corporate, TemplateCategory::Other, null => '01 Kurumsal Belgeler',
        };
    }

    public static function rootName(string $companyName): string
    {
        $safe = preg_replace('/[\\\\\\/:*?"<>|]+/u', '-', trim($companyName)) ?: 'Firma';

        return 'KVKK360/'.$safe;
    }
}
