<?php

namespace App\Domain\Audits\Enums;

enum AuditType: string
{
    case Internal = 'internal';
    case External = 'external';
    case Authority = 'authority';
    case Camera = 'camera';
    case Supplier = 'supplier';
    case Process = 'process';
    case Other = 'other';
}
