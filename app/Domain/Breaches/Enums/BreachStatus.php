<?php

namespace App\Domain\Breaches\Enums;

enum BreachStatus: string
{
    case Draft = 'draft';
    case Investigating = 'investigating';
    case AuthorityNotified = 'authority_notified';
    case SubjectsNotified = 'subjects_notified';
    case Closed = 'closed';
}
