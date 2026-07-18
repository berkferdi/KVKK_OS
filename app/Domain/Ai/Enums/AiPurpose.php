<?php

namespace App\Domain\Ai\Enums;

enum AiPurpose: string
{
    case DocumentDraft = 'document_draft';
    case FindingsSummary = 'findings_summary';
}
