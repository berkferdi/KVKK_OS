<?php

namespace App\Domain\Shared\Enums;

enum TenantStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Trial = 'trial';
}
