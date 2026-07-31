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
            '03 Aydınlatma',
            '04 Açık Rıza',
            '05 Formlar',
            '06 Sözleşmeler',
            '07 Taahhütnameler',
            '08 Prosedürler',
            '09 Envanter',
            '10 Raporlar',
            '11 Talimatlar',
            '12 Diğer',
            '13 VERBİS',
            '14 Eğitim',
            '15 Teslim Dosyası',
        ];
    }

    public static function folderForCategory(?TemplateCategory $category): string
    {
        return match ($category) {
            TemplateCategory::Corporate => '01 Kurumsal Belgeler',
            TemplateCategory::Policy => '02 Politikalar',
            TemplateCategory::Disclosure => '03 Aydınlatma',
            TemplateCategory::Consent => '04 Açık Rıza',
            TemplateCategory::Form => '05 Formlar',
            TemplateCategory::Contract => '06 Sözleşmeler',
            TemplateCategory::Commitment => '07 Taahhütnameler',
            TemplateCategory::Procedure => '08 Prosedürler',
            TemplateCategory::Inventory => '09 Envanter',
            TemplateCategory::Report => '10 Raporlar',
            TemplateCategory::Instruction => '11 Talimatlar',
            TemplateCategory::Other, null => '12 Diğer',
        };
    }

    public static function rootName(string $companyName): string
    {
        $safe = preg_replace('/[\\\\\\/:*?"<>|]+/u', '-', trim($companyName)) ?: 'Firma';

        return 'KVKK360/'.$safe;
    }
}
