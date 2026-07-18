<?php

namespace App\Domain\Inventory\Enums;

enum LegalBasis: string
{
    case ExplicitConsent = 'explicit_consent';
    case Contract = 'contract';
    case LegalObligation = 'legal_obligation';
    case VitalInterest = 'vital_interest';
    case PublicInterest = 'public_interest';
    case LegitimateInterest = 'legitimate_interest';
    case ExplicitConsentSensitive = 'explicit_consent_sensitive';
    case Law = 'law';
    case ExplicitlyMadePublic = 'explicitly_made_public';
    case EstablishmentOfRight = 'establishment_of_right';
    case Protective = 'protective';
}
