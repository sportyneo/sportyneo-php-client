<?php

namespace Sportyneo\SDK\Contracts;

enum PaymentMethod: int
{
    case ALMA = 1;
    case FLOA = 2;
    case STRIPE = 3;
    case CB = 4;
    case CHECK = 5;
    case CASH = 6;
    case RYFT = 7;

    public function label(): string
    {
        return match ($this) {
            self::ALMA => 'ALMAPAY',
            self::FLOA => 'FLOAPAY',
            self::STRIPE => 'STRIPE',
            self::CB => 'CB',
            self::CHECK => 'CHECK',
            self::CASH => 'COD',
            self::RYFT => 'RYFT',
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::ALMA => 'Alma',
            self::FLOA => 'Floa',
            self::STRIPE => 'Stripe',
            self::CB => 'Carte Bancaire',
            self::CHECK => 'Chèque',
            self::CASH => 'Espèces',
            self::RYFT => 'Ryft',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match (strtoupper($label)) {
            'ALMAPAY' => self::ALMA,
            'FLOAPAY' => self::FLOA,
            'STRIPE' => self::STRIPE,
            'CB' => self::CB,
            'CHECK' => self::CHECK,
            'COD' => self::CASH,
            'RYFT' => self::RYFT,
            default => null,
        };
    }

    public static function getIdFromLabel(string $label): ?int
    {
        $enum = self::fromLabel($label);

        return $enum?->value;
    }

    public static function getDefault(): self
    {
        return self::CB;
    }

    public static function getSelectArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->displayName();
        }

        return $result;
    }

    public function isOnline(): bool
    {
        return in_array($this, [self::ALMA, self::FLOA, self::STRIPE, self::CB, self::RYFT]);
    }

    public function isPhysical(): bool
    {
        return in_array($this, [self::CHECK, self::CASH]);
    }

    public static function validationRules(): array
    {
        return [
            'sometimes',
            function ($attribute, $value, $fail) {
                if (is_numeric($value) && ! self::tryFrom((int) $value)) {
                    $fail("La valeur de $attribute n'est pas un identifiant de méthode de paiement valide.");
                }

                if (is_string($value) && ! is_numeric($value) && ! self::fromLabel($value)) {
                    $fail("La valeur de $attribute n'est pas une méthode de paiement valide.");
                }
            },
            'max:255',
        ];
    }

    public static function fromInput($value): ?self
    {
        if (is_numeric($value)) {
            return self::tryFrom((int) $value);
        }

        if (is_string($value)) {
            return self::fromLabel($value);
        }

        return null;
    }

    public static function getLogo($value, $format = 'svg'): string
    {
        if(!in_array($format, ['svg', 'png', 'jpeg', 'jpg', 'webp', 'gif']))
            $format = 'svg';

        return match ($value) {
            self::ALMA => 'alma.'.$format,
            self::FLOA => 'floa.'.$format,
            self::STRIPE => 'stripe.'.$format,
            self::CB => 'cb.'.$format,
            self::CHECK => 'check.'.$format,
            self::CASH => 'cash.'.$format,
            self::RYFT => 'ryft.'.$format,
        };
    }
}
