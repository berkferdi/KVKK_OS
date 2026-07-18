<?php

namespace App\Domain\Risk\Enums;

enum RiskAssessmentStatus: string
{
    case Open = 'open';
    case Mitigating = 'mitigating';
    case Accepted = 'accepted';
    case Closed = 'closed';
}
