<?php

namespace App\Domain\Ai\Enums;

enum AiGenerationStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
}
