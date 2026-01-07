<?php

namespace Sportyneo\SDK\Contracts;

enum DiscountType: int
{
    case LOCAL = 1;
    case REGION = 2;
    case NATION = 3;
    case ANCV = 4;
    case CUSTOM = 5;

    public function slug(): string
    {
        return match ($this) {
            self::LOCAL => 'local',
            self::REGION => 'region',
            self::NATION => 'nation',
            self::ANCV => 'ancv',
            self::CUSTOM => 'custom',
        };
    }

    public static function fromSlug(string $slug): self
    {
        return match ($slug) {
            'local' => self::LOCAL,
            'region' => self::REGION,
            'nation' => self::NATION,
            'ancv' => self::ANCV,
            'custom' => self::CUSTOM,
            default => throw new \ValueError("Invalid slug: {$slug}"),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::LOCAL => 'Local',
            self::REGION => 'Regionale',
            self::NATION => 'Nationale',
            self::ANCV => 'ANCV',
            self::CUSTOM => 'Promotion',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }

    public static function getSelectArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }
}