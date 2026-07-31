<?php

namespace App\Domain\Backup\Enums;

enum BackupStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
}
