<?php

namespace App\Domain\Applications\Enums;

enum ApplicationRequestType: string
{
    case Access = 'access';
    case Rectification = 'rectification';
    case Erasure = 'erasure';
    case Restriction = 'restriction';
    case Portability = 'portability';
    case Objection = 'objection';
    case Other = 'other';
}
