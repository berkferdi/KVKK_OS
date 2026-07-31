<?php

namespace App\Domain\Documents\Enums;

enum ProcedureCategory: string
{
    case DataSubjectRequest = 'data_subject_request';
    case Breach = 'breach';
    case AccessControl = 'access_control';
    case Retention = 'retention';
    case Training = 'training';
    case Camera = 'camera';
    case Other = 'other';
}
