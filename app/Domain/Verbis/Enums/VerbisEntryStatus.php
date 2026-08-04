<?php

namespace App\Domain\Verbis\Enums;

enum VerbisEntryStatus: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case Submitted = 'submitted';
    case Archived = 'archived';
}
