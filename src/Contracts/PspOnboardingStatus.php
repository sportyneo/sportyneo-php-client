<?php

namespace App\Enums;

enum PspOnboardingStatus: string
{
    case Pending = 'pending';               // Compte créé, onboarding pas encore commencé
    case ActionRequired = 'action_required'; // Documents / infos manquants
    case Verified = 'verified';             // Onboarding complet, peut encaisser
    case Rejected = 'rejected';             // Rejeté par le PSP
    case Suspended = 'suspended';           // Suspendu

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::ActionRequired => 'Action requise',
            self::Verified => 'Vérifié',
            self::Rejected => 'Rejeté',
            self::Suspended => 'Suspendu',
        };
    }

    public function canProcessPayments(): bool
    {
        return $this === self::Verified;
    }
}
