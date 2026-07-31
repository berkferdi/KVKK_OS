<?php

namespace App\Domain\Inventory\Enums;

enum ProcessingActivityStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
