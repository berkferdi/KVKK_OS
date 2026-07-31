<?php

namespace App\Domain\Cameras\Enums;

enum CameraStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Planned = 'planned';
}
