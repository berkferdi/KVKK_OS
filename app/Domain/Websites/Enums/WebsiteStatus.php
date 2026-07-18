<?php

namespace App\Domain\Websites\Enums;

enum WebsiteStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Draft = 'draft';
}
