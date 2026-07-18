<?php

namespace App\Domain\Documents\Enums;

enum TemplateCategory: string
{
    case Corporate = 'corporate';
    case Policy = 'policy';
    case Camera = 'camera';
    case Web = 'web';
    case Cookie = 'cookie';
    case Other = 'other';
}
