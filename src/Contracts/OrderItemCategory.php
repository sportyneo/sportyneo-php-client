<?php

namespace Sportyneo\SDK\Contracts;

enum OrderItemCategory: int
{
    case LICENSES = 1;
    case STAGES = 2;
    case AUTRES = 3;
    case BOUTIQUE = 4;
    case PLANNING = 5;
    case MERCHANDISING = 6;
    public function isDiscountable(): bool
    {
        return match($this) {
            self::LICENSES, self::STAGES => true,
            default => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::LICENSES => 'Adhésions',
            self::STAGES => 'Stages',
            self::AUTRES => 'Autres',
            self::BOUTIQUE => 'Boutique',
            self::PLANNING => 'Planning',
            self::MERCHANDISING => 'Merchandising',
        };
    }

    public function fromSlug(string $slug): self
    {
        return match($slug) {
            'adhesion' => self::LICENSES,
            'stages' => self::STAGES,
            'autres' => self::AUTRES,
            'boutique' => self::BOUTIQUE,
            'planning' => self::PLANNING,
            'merchandising' => self::MERCHANDISING,
            default => throw new \ValueError("Invalid slug: {$slug}"),
        };
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