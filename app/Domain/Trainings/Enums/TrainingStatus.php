<?php

namespace App\Domain\Trainings\Enums;

enum TrainingStatus: string
{
    case Planned = 'planned';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
