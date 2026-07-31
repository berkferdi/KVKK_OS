<?php

namespace App\Domain\Organization\Enums;

enum CompanyStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
