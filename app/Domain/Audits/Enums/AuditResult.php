<?php

namespace App\Domain\Audits\Enums;

enum AuditResult: string
{
    case Pending = 'pending';
    case Compliant = 'compliant';
    case MinorNonconformity = 'minor_nonconformity';
    case MajorNonconformity = 'major_nonconformity';
    case NotApplicable = 'not_applicable';
}
