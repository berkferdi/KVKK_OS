<?php

namespace App\Domain\Cameras\Enums;

enum CameraType: string
{
    case Indoor = 'indoor';
    case Outdoor = 'outdoor';
    case Vehicle = 'vehicle';
    case Other = 'other';
}
