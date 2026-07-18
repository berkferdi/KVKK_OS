<?php

namespace App\Domain\Audits\Enums;

enum AuditStatus: string
{
    case Planned = 'planned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
