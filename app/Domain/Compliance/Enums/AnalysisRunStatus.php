<?php

namespace App\Domain\Compliance\Enums;

enum AnalysisRunStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
}
