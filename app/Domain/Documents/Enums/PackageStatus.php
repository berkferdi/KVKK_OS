<?php

namespace App\Domain\Documents\Enums;

enum PackageStatus: string
{
    case Ready = 'ready';
    case Failed = 'failed';
    case Superseded = 'superseded';
}
