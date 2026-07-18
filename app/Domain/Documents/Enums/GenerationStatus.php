<?php

namespace App\Domain\Documents\Enums;

enum GenerationStatus: string
{
    case Generated = 'generated';
    case Failed = 'failed';
    case Superseded = 'superseded';
}
