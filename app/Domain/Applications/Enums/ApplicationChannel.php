<?php

namespace App\Domain\Applications\Enums;

enum ApplicationChannel: string
{
    case Email = 'email';
    case Web = 'web';
    case Post = 'post';
    case InPerson = 'in_person';
    case Other = 'other';
}
