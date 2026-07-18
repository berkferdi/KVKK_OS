<?php

namespace App\Domain\Verbis\Enums;

enum VerbisRegistrationStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Registered = 'registered';
    case UpdateRequired = 'update_required';
    case Exempt = 'exempt';
    case NotApplicable = 'not_applicable';
}
