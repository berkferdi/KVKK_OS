<?php

namespace App\Domain\Applications\Enums;

enum ApplicationStatus: string
{
    case Received = 'received';
    case InProgress = 'in_progress';
    case Responded = 'responded';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';
}
