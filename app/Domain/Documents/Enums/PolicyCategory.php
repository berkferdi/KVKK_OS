<?php

namespace App\Domain\Documents\Enums;

enum PolicyCategory: string
{
    case Privacy = 'privacy';
    case Retention = 'retention';
    case Cookie = 'cookie';
    case Access = 'access';
    case Security = 'security';
    case Camera = 'camera';
    case Other = 'other';
}
