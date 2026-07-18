<?php

namespace App\Domain\Trainings\Enums;

enum TrainingType: string
{
    case Awareness = 'awareness';
    case KvkkBasics = 'kvkk_basics';
    case Camera = 'camera';
    case DataSecurity = 'data_security';
    case IncidentResponse = 'incident_response';
    case RoleBased = 'role_based';
    case Other = 'other';
}
