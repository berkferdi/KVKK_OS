<?php

namespace App\Domain\Documents\Enums;

enum TemplateCategory: string
{
    case Corporate = 'corporate';
    case Policy = 'policy';
    case Disclosure = 'disclosure';
    case Consent = 'consent';
    case Form = 'form';
    case Contract = 'contract';
    case Commitment = 'commitment';
    case Procedure = 'procedure';
    case Inventory = 'inventory';
    case Report = 'report';
    case Instruction = 'instruction';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Corporate => 'Kurumsal',
            self::Policy => 'Politika',
            self::Disclosure => 'Aydınlatma',
            self::Consent => 'Açık Rıza',
            self::Form => 'Form',
            self::Contract => 'Sözleşme',
            self::Commitment => 'Taahhütname',
            self::Procedure => 'Prosedür',
            self::Inventory => 'Envanter',
            self::Report => 'Rapor',
            self::Instruction => 'Talimat',
            self::Other => 'Diğer',
        };
    }
}
