<?php

namespace App\Domain\Breaches\Enums;

enum BreachType: string
{
    case Confidentiality = 'confidentiality';
    case Integrity = 'integrity';
    case Availability = 'availability';
    case Other = 'other';
}
