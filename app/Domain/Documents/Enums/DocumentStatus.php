<?php

namespace App\Domain\Documents\Enums;

enum DocumentStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case UnderReview = 'under_review';
    case Archived = 'archived';
}
