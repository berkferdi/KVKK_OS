<?php

namespace App\Domain\Customers\Enums;

enum CustomerType: string
{
    case Individual = 'individual';
    case Corporate = 'corporate';
}
