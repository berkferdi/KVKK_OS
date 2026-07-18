<?php

namespace App\Domain\Trainings\Enums;

enum TrainingDeliveryMethod: string
{
    case InPerson = 'in_person';
    case Online = 'online';
    case Hybrid = 'hybrid';
    case SelfStudy = 'self_study';
}
