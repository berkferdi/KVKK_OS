<?php

namespace App\Domain\Cookies\Enums;

enum CookieCategory: string
{
    case Necessary = 'necessary';
    case Functional = 'functional';
    case Analytics = 'analytics';
    case Marketing = 'marketing';
    case Other = 'other';
}
