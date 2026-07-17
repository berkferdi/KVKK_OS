<?php

namespace App\Domain\Personnel\Enums;

enum EmployeeStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Left = 'left';
}
