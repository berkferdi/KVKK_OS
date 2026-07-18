<?php

namespace App\Domain\Visitors\Enums;

enum VisitorStatus: string
{
    case Expected = 'expected';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';
}
