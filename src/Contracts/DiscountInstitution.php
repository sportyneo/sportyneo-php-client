<?php

namespace Sportyneo\SDK\Contracts;

/**
 * Catalogue des institutions de remise supportées par la plateforme Sportyneo.
 *
 * Chaque case représente une institution avec son slug unique, sa portée géographique,
 * et indique si elle nécessite un handler API dédié côté serveur.
 */
enum DiscountInstitution: string
{
    case PassCommune = 'pass_commune';
    case PassRegion = 'pass_region';
    case PassRegionRhoneAlpes = 'pass_region_rhone_alpes';
    case PassSport = 'pass_sport';

    public function label(): string
    {
        return match ($this) {
            self::PassCommune => 'Pass Commune',
            self::PassRegion => 'Pass Région',
            self::PassRegionRhoneAlpes => 'Pass Région Rhône-Alpes',
            self::PassSport => 'Pass Sport',
        };
    }

    /**
     * Portée géographique : 'national', 'regional', 'local'.
     */
    public function scope(): string
    {
        return match ($this) {
            self::PassCommune => 'local',
            self::PassRegion => 'regional',
            self::PassRegionRhoneAlpes => 'regional',
            self::PassSport => 'national',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn ($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
