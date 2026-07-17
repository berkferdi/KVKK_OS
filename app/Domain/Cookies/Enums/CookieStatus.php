<?php

namespace App\Domain\Cookies\Enums;

enum CookieStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
