<?php

namespace App\Domain\Suppliers\Enums;

enum SupplierType: string
{
    case Goods = 'goods';
    case Services = 'services';
    case Both = 'both';
}
